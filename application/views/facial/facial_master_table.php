<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

?>
<table class="table table-striped table-bordered table-hover" id="MasterTable">
    <thead>                     
        <tr >
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>Device I.D</b></th>
            <th><b>Name</b></th>
            <th><b>Person Count</b></th>
            <th><b>Face Count</b></th>
            <th><b>Device Version</b></th>
            <th><b>I.P Adress</b></th>
            <th><b>Last Logged</b></th>
            <th><b>Status</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
            <?php 
            $start_date = new DateTime(date($today));
            $since_start = $start_date->diff(new DateTime(date($row->timestamp)));
            ?>
        <tr>
            <td class="align_center">
               <a code="<?= $row->deviceKey ?>" codename="<?= $row->name ?>" class="btn btn-primary history"><i class="glyphicon glyphicon-cog"></i></a>
            </td>
            <td><?= $row->deviceKey ?></td>
            <td><?= $row->name ?></td>
            <td><?= $row->personCount ?></td>
            <td><?= $row->faceCount ?></td>
            <td><?= $row->version ?></td>
            <td><?= $row->ip ?></td>
            <td><?= date("Y-m-d H:i:s", substr($row->time, 0, -3)); ?></td>
            <td><?= ($since_start->i > 5)? "<h5 style='color:red'>DISCONNECTED</h5>":"<h5 style='color:green'>CONNECTED</h5>" ?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
    
</table>
<script>

$(document).ready(function(){
    var table = $('#MasterTable').DataTable({
        deferRender: true
    });
    new $.fn.dataTable.FixedHeader( table );
    $(".chosen").chosen();
});

$("#MasterTable").on("click", ".history", function(){
    code = $(this).attr('code');
    $("#employeeid").attr('deviceKey', code);
    $("#facial_name").html("<b>" + code + "</b>");
    $("#name").val($(this).attr('codename'));
    $("#permission").modal();
    loadTeachingEmployee(code);
});

$("#Save").unbind('click').bind('click', function(event) {
    var emp   =   $("#employeeid").val();
    var deviceKey   =   $("#employeeid").attr("deviceKey");
    var name   =   $("#name").val();
    if (emp == null) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please Select Users!',
            timer: 1500
        })
        return;
    }

    if (name == null) {
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'Please add name!',
            timer: 1500
        })
        return;
    }
    $.ajax({

       url      :   "<?=site_url("facial_/savePermissionMaster")?>",
       type     :   "POST",
       data     :   {emp:emp.toString(), deviceKey:deviceKey, name:name},
       success  :   function(msg){
            if (msg == "success") {
                $('#permission').modal('toggle');
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Permission Set Successfully',
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
            loadFacialMaster()
       }
    });
});

$(".deleteRecord").click(function(){
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/resetLogs')?>",
        data: {serial_number:serial_number},
        success:function(response){
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Data has been deleted successfully',
                showConfirmButton: true,
                timer: 1500
            })
            var deleteRecordTask = '"pass":"12345678",time:"'+ today +'"';
            var deleteRecordinterface = 'deleteRecords';
            $.ajax({
                type: "POST",
                url: "<?= site_url('facial_/saveTaskToDevice')?>",
                data: {serial_number:serial_number,interface:deleteRecordinterface,task:deleteRecordTask,ip:ip},
                success:function(response){

                }
            });
            var deleteRecordICTask = '"pass":"12345678",time:"'+ today +'"';
            var deleteRecordICinterface = 'deleteICRecords';
            $.ajax({
                type: "POST",
                url: "<?= site_url('facial_/saveTaskToDevice')?>",
                data: {serial_number:serial_number,interface:deleteRecordICinterface,task:deleteRecordICTask,ip:ip},
                success:function(response){

                }
            });
            loadFacialDevice(serial_number,ip);
        }
    });
});

$(".reset").click(function(){
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/resetDevice')?>",
        data: {serial_number:serial_number},
        success:function(response){
            var resetTask = '"pass":"12345678",delete:false';
            var resetInterface = 'device/reset';
            $.ajax({
                type: "POST",
                url: "<?= site_url('facial_/saveTaskToDevice')?>",
                data: {serial_number:serial_number,interface:resetInterface,task:resetTask,ip:ip},
                success:function(response){

                }
            });
            loadFacialDevice(serial_number,ip);
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Data has been deleted successfully',
                showConfirmButton: true,
                timer: 1500
            })
        }
    });
});

$(".deleteDeviceRecord").click(function(){
    var deleteRecordTask = '"pass":"12345678",time:"'+ today +'"';
    var deleteRecordinterface = 'deleteRecords';
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/saveTaskToDevice')?>",
        data: {serial_number:serial_number,interface:deleteRecordinterface,task:deleteRecordTask,ip:ip},
        success:function(response){

        }
    });
    var deleteRecordICTask = '"pass":"12345678",time:"'+ today +'"';
    var deleteRecordICinterface = 'deleteICRecords';
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/saveTaskToDevice')?>",
        data: {serial_number:serial_number,interface:deleteRecordICinterface,task:deleteRecordICTask,ip:ip},
        success:function(response){

        }
    });
    Swal.fire({
        icon: 'success',
        title: 'Success!',
        text: 'Data has been deleted successfully',
        showConfirmButton: true,
        timer: 1500
    })
    loadFacialDevice(serial_number,ip);
});

function loadTeachingEmployee(code){
    $.ajax({
        url: "<?= site_url('facial_/loadEmpPermissionFacialMaster') ?>",
        type: "POST",
        data: {
            deviceKey: code
        },
        success:function(response){
            $("#employeeid").html(response).trigger("chosen:updated");
        }
    });
}

</script> 