$(document).ready(function(){
    $(".gateswitch").each(function(){
        var isActive = $(this).attr("activity");
        var username = $(this).attr("username")
        if(isActive == "Yes") $(".actionBtn_"+username).css("display", "none");
    });
});

$(".gateswitch").click(function(){
    var isActive = $(this).attr("activity");
    var username = $(this).attr("username");
    if(isActive == "Yes") $(".actionBtn_"+username).css("display", "unset");
});

$("#userstable_length").append("<span id='errormsg' style='padding-left: 20px; font-weight:bold;'></span>");

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

$(".editbtn").click(function(){
    var id = $(this).attr('eid');
    $.ajax({
        url: $("#site_url").val() + "/machine_/getTerminalData",
        type: "POST",
        data: {id: id},
        success:function(response){
            $("#modal-view").modal(); 
            $("#modal-view").find("h3[tag='title']").text("Edit Terminal Setup");
            $("#modal-view").find("#button_save_modal").text("Save");
            $("#modal-view").find("#button_save_modal").addClass("edit_btn");
            $("#modal-view").find("div[tag='display']").html(response);
            $("#modal-view").find("input[name='username']").prop('readonly', true);
        }
    });
});




$(".delbtn").click(function(){
    const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var id = $(this).attr('did');
            $.ajax({
                url: $("#site_url").val() + "/machine_/deleteTerminalData",
                type: "POST",
                data: {id: id},
                success:function(response){
                    Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Terminal has been deleted successfully',
                          showConfirmButton: true,
                          timer: 1000
                    })
                    loadTerminalList();
                }
            });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data is safe.',
              'error'
            )
          }
        })

});

function loadTerminalList(){
    $.ajax({
        url: $("#site_url").val() + "/machine_/loadTerminalList",
        success:function(response){
            $("#gate_user_list").html(response);
        }
    });
}
