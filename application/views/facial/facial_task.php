<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
$today = date('Y-m-d g:i:s');
?>
<br><br>
<button class="btn btn-primary" id="clearTask">Clear Task Log</button>
<table class="table table-striped table-bordered table-hover" id="taskTable">
    <thead>                        
        <tr>
            <th><b>Time</b></th>
            <th><b>Interface</b></th>
            <th><b>Status</b></th>
            <th><b>Device I.D</b></th>
            <th><b>ID</b></th>
            <th><b>IP</b></th>
        </tr>
    </thead>
    <tbody>
    </tbody>
</table>
<script>
var serial_number = "<?= $deviceKey ?>";
var today = "<?= $today ?>";

$(document).ready(function(){
    var oTable = $("#taskTable").DataTable({ 
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= site_url('facial_/getLogsTask')?>", 
            data: function (data) { 
                data.today = today
                data.serial_number = serial_number
            }   
        },
        "drawCallback": function( settings ) {
            $("#taskTable").delegate("tr", "click", function() {

            });   
        },
        deferRender: true,
        searching: true,
        pageLength: 10, 
        lengthChange: false,  
        order: [[0 , "desc"],[ 1, "desc" ]]  
    });
});

$("#clearTask").unbind('click').bind('click', function(event) {
    $.ajax({
       url      :   "<?=site_url("facial_/clearFacialTask")?>",
       type     :   "POST",
       data     :   {deviceKey:serial_number},
       success  :   function(msg){
            if (msg) {
                $('#permission').modal('toggle');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Task has been cleared successfully',
                    showConfirmButton: true,
                    timer: 1500
                })
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Please Check Connection!',
                    timer: 1500
                })
            }
            $("#taskTable").DataTable().draw();
       }
    });
});
</script>