var loading = $("#loading").html(); /*loading gif*/
$(document).ready(function(){
   loadContent(); 
});

$('.chosen').chosen();     
$("#datesetfrom,#datesetto").datepicker({
    autoclose: true,
    todayBtn : true
});

function reloaddata(){
    if($("#category").val() == "Message"){
        loadContent();
    }else if($("#category").val() == "processdtr"){
     loadProcessDTR();   
    }else{
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Category is empty.. Failed to refresh data..',
            showConfirmButton: true,
            timer: 1000
        });
    }
}

function loadContent(){
    var form_data = {
                        cat: $("#category").val(),
                        cutoff: $("#cutoff").val(),
                        view: "process/displaycutoff"
    };
    if($("#category").val() != ""){
        $("#contents").show();
        $("#contents").html(loading);
        $.ajax({
            url: $("#site_url").val() + "/main/siteportion",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#contents").html(msg);
            }
        });
    }
}

function loadProcessDTR(){
    $("#contents").show();
    $("#contents").html(loading);
    $.ajax({
        url: $("#site_url").val() + "/setup_/loadProcessDTR",
        success: function(msg){
            $("#contents").html(msg);
        }
    });
}

function notifyEmployeeConfirmation(){
    $("#notif_loading").show();
    $.ajax({
        url: $("#site_url").val() + "/attendance_/getEmployeeListToConfirm",
        dataType: "json",
        success: function(response){
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: response.msg,
                showConfirmButton: true,
                timer: 1000
            });
            $("#notif_loading").hide();
        }
    });
}