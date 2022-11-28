<link href="<?=base_url();?>css/terminal_setup/terminal.css" rel="stylesheet">
<div id="content"> <!-- Content start -->
    <a id="addnewterminal"><i class="icon-plus-sign"></i> Add New</a>
    <div class="widgets_area">
        <div class="row-fluid">
            <div class="span12">
                <div class="well blue">
                    <div class="well-header">
                        <h5>User List</h5>
                        <input type="hidden" id="site_url" value="<?= site_url(); ?>">
                    </div>

                    <div class="well-content" id="gate_user_list"></div>
                </div>

                    <div class="well blue">
                        <div class="well-header">
                            <h5>Gate History</h5>
                        </div>

                        <div class="well-content" id="gate_history"></div>
                    </div>
            </div>
        </div>
    </div>    
</div> 

<!-- Modal -->
<div class="modal fade" id="manage_machine" role="dialog"></div>

<!-- <script src="<=base_url()?>js/terminal_setup/terminal.js"></script> -->
<script type="text/javascript">
$(document).ready(function(){
function loadTerminalList(){
    $.ajax({
        url: $("#site_url").val() + "/machine_/loadTerminalList",
        success:function(response){
            $("#gate_user_list").html(response);
            $("#modal-view").find("#button_save_modal").removeClass();
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
});
  
$("#addnewterminal").click(function(){
    $.ajax({
        url: $("#site_url").val() + "/machine_/manageGateAccount",
        success:function(response){
            $("#modal-view").modal();
            $("#modal-view").find("h3[tag='title']").text("Add Terminal Setup");
            $("#modal-view").find("#button_save_modal").removeClass();
            $("#modal-view").find("#button_save_modal").addClass("btn btn-success add_btn");
            $("#modal-view").find("#button_save_modal").text("Save");
            $("#modal-view").find("div[tag='display']").html(response);
        }
    });
});

    // $('select').chosen();

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
//             $("#modal-view").modal(); 
//             $("#modal-view").find("h3[tag='title']").text("Edit Terminal Setup");

//             $("#modal-view").find("div[tag='display']").html(response);
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




</script>