<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
$today = date('Y-m-d g:i:s');
// $action
?>

<div class="panel animated fadeIn">
    <div class="panel-heading" id="setupTable"><h4><b>Facial Devices Logs </b></h4></div>
    <div class="panel-body" id="data_table">
        <table class="table table-striped table-bordered table-hover" id="logsTable">
            <thead>                        
                <tr >
                    <th><b>Time</b></th>
                    <th><b>I.P</b></th>
                    <th><b>Name</b></th>
                    <th><b>Employee ID</b></th>
                    <th><b>Login Type</b></th>
                    <th><b>Device I.D</b></th>
                    <th><b>Image</b></th>
                </tr>
            </thead>
            <tbody>
                
            </tbody>
        </table>
    </div>
</div>
<script>
var ip = "<?= $ip ?>";
var serial_number = "<?= $deviceKey ?>";
var today = "<?= $today ?>";
$(document).ready(function(){
    var oTable = $("#logsTable").DataTable({ 
        processing: true,
        serverSide: true,
        ajax: {
            url: "<?= site_url('facial_/getLogs')?>", 
            data: function (data) { 
                data.today = today
                data.ip = ip
                data.serial_number = serial_number
            }  
        },
        deferRender: true,
        searching: true,
        pageLength: 10, 
        lengthChange: false,  
        order: [[0 , "desc"],[ 1, "desc" ]]  
    });
});

$("#load").click(function(){
   loadFacialDevice(serial_number,ip);
});

</script>