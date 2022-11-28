loadTerminalList();
loadGateHistoryList();
  
$("#addnewterminal").click(function(){
    $.ajax({
        url: $("#site_url").val() + "/machine_/manageGateAccount",
        success:function(response){
            $("#dtr-modal").find("h3[tag='title']").text("Add Terminal Setup");
            $("#dtr-modal").find("#save-dtr-setup").removeClass();
            $("#dtr-modal").find("#save-dtr-setup").addClass("btn btn-success add_btn");
            $("#dtr-modal").find("#save-dtr-setup").text("Save");
            $("#dtr-modal").find("div[tag='display']").html(response);
        }
    });
});

function loadTerminalList(){
    $.ajax({
        url: $("#site_url").val() + "/machine_/loadTerminalList",
        success:function(response){
            $("#gate_user_list").html(response);
        }
    });
}

function loadGateHistoryList(){
    $.ajax({
        url: $("#site_url").val() + "/machine_/loadGateHistoryList",
        success:function(response){
            $("#gate_history").html(response);
        }
    });
}

$('input').on('click',function(e){
    $(this).attr('disabled','true');
    var online_id = $(this).attr('online_id');
    var username = $(this).attr('username');

    if(online_id){
     $.ajax({
        url: $("#site_url").val() + "/maintenance_/forceLogout",
        type:"POST",
        data:{
           online_id : online_id
        },
        success: function(msg){
            if(msg=='1'){
                $('#errormsg').html('User '+username+' : Successfully logged out.').css('color','green').show().delay(10000).fadeOut();
            }else{
                $('#errormsg').html('User '+username+' : Failed to log out.').css('color','red').show().delay(10000).fadeOut();
            }
        }
     }); 
 }   

});

// $(".editbtn").click(function(){
//     var id = $(this).attr('eid');
//     $.ajax({
//         url: $("#site_url").val() + "/machine_/getTerminalData",
//         type: "POST",
//         data: {id: id},
//         success:function(response){
//             $("#dtr-modal").modal(); 
//             $("#dtr-modal").find("h3[tag='title']").text("Edit Terminal Setup");

//             $("#dtr-modal").find("div[tag='display']").html(response);
//             $(".modalclose").click();
//         }
//     });
// });

// $(".delbtn").click(function(){
//     var id = $(this).attr('did');
//         $.ajax({
//             url: $("#site_url").val() + "/machine_/deleteTerminalData",
//             type: "POST",
//             data: {id: id},
//             success:function(response){
//                 loadTerminalList();
//             }
//         });
// });

$("#campus_allowed").change(function(){
    $("#employee_allowed").val('').trigger("liszt:updated");
});
$("#employee_allowed").change(function(){
    $("#campus_allowed").val('').trigger("liszt:updated");
});




// $("select").chosen();
//  $(".chzn-select").chosen();



