
// $("#campus_allowed").change(function(){
// 	$("#employee_allowed").val('').trigger("liszt:updated");
// });
// $("#employee_allowed").change(function(){
// 	$("#campus_allowed").val('').trigger("liszt:updated");
// });

// $(".add_btn").click(function(){
// 	$(".add_btn").unbind();
// 	var formdata = $("#machine_form").serialize();
// 	$.ajax({
// 		url: $("#site_url").val() + "/machine_/validateGateAccount",
// 		type: "POST",
// 		data: formdata,
// 		success:function(){
// 			alert("Successfully saved terminal information!");
// 			$("#modal-view").modal('toggle');
// 			$(".modalclose").click();
// 			loadTerminalList();
// 		}
// 	});
// });

// $(".edit_btn").click(function(){
//     $(".edit_btn").unbind();
//     var formdata = $("#machine_form").serialize();
//     $.ajax({
//         url: $("#site_url").val() + "/machine_/validateGateAccount",
//         type: "POST",
//         data: formdata,
//         success:function(){
//             alert("Successfully updated terminal information!");
//             $("#modal-view").modal('toggle');
//             $(".modalclose").click();
//             loadTerminalList();
//         }
//     });
// });

// $("select").chosen();
// 	$(".chzn-select").chosen();


// function loadTerminalList(){
//     $.ajax({
//         url: $("#site_url").val() + "/machine_/loadTerminalList",
//         success:function(response){
//             $("#gate_user_list").html(response);
//         }
//     });
// }