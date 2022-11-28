<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
$today = date('Y-m-d g:i:s');
?>
<table class="table table-striped table-bordered table-hover" id="newTablePersonList">
    <thead>                      
        <tr >
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>Device I.D</b></th>
            <th><b>Person ID</b></th>
            <th><b>Name</b></th>
            <th><b>RFID Card</b></th>
            <th><b>Employee ID</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <a empid="<?= $row->personId ?>" code="<?= $row->serial_number ?>" class="btn btn-primary editbtn" action="add" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?= $row->personId ?>" empname="<?= $row->fullname ?>" class="btn btn-danger delete"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;&nbsp;
                <?php if ($devicemodel == "RA"){ ?>
                    <a code="<?= $row->serial_number ?>" personid="<?= $row->personId ?>" model="RA" class="btn btn-info indevice"><i class="glyphicon glyphicon-camera"></i></a>
                <?php }else{ ?>
                    <a code="<?= $row->serial_number ?>" personid="<?= $row->personId ?>" model="FR" class="btn btn-info indevice"><i class="glyphicon glyphicon-camera"></i></a>
                <?php } ?>
            </td>
            <td><?= $row->serial_number ?></td>
            <td><?= $row->personId ?></td>
            <td><?= $row->fullname ?></td>
            <td><?= $row->card ?></td>
            <td><?= $row->employeeid ?></td>
        </tr>
        <?php 
        endforeach ?>
    </tbody>
</table>

<div id="deletemodal" class="modal fade" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <div class="media">
            <div class="media-left">
                <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
            </div>
            <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                 <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
            </div>
        </div>
        <center><b><h3 tag="title" class="modal-title">Delete Person To Device</h3></b></center>
          </div>
          <div class="modal-body">
            <input type="hidden" name="deletePerson" id="deletePerson" value="">
            <p>Are you sure you want to Remove <span id="facial_name"></span> from Facial Device?</p>
          </div>
          <div class="modal-footer">
            <button type="button" id="delete" class="btn btn-danger" data-dismiss="modal">Yes</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
          </div>
        </div>
        
    </div>
</div>
<script>
var ip = $("#ip").val();
var serial_number = $("#deviceKey").val();
var today = "<?= $today ?>";

$(document).ready(function(){
    var table = $('#newTablePersonList').DataTable({
        "lengthMenu": [[10, 25, 50, -1], [10, 25, 50, "All"]]
    });
    new $.fn.dataTable.FixedHeader( table );
});

$("#newTablePersonList").on("click", ".editbtn", function(){
    var code = '';
    var action = '';
    empid = $(this).attr('empid');
    code = $(this).attr('code');
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/managePerson')?>",
        data: {empid:empid,code:code,action:"Edit"},
        success:function(response){
            $("#modal-view").find("div[tag='display']").html(response);
            $("#modal-view").find(".modal-title").html("Edit Person");
        }
    });
});

$("#newTablePersonList").on("click", ".indevice", function(){
    var personId = $(this).attr('personid'); 
    var code = $(this).attr('code'); 
    var model = $(this).attr('model');
    if (model == "RA") {
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'In-device Registration',
          text: "Please make sure the person is in front of the device with 1 meter distance and remove any facial accessories. It might take 1 minute before the in device registration process show.",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes',
          cancelButtonText: 'No',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var taskData = '"pass":"12345678","personId":"'+ personId +'"';
            var interface = 'face/takeImg';
            $.ajax({
                type: "POST",
                url: "<?= site_url('facial_/saveTaskToDevice')?>",
                data: {serial_number:code,interface:interface,task:taskData},
                success:function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Please wait 1 min to initiate the registration.',
                        showConfirmButton: true
                    })
                }
            });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data is safe',
              'error'
            )
          }
        })
    }else{
        $.ajax({
            type: "POST",
            url: "<?= site_url('facial_/indeviceRegistrationFR')?>",
            data: {serial_number:code,personId:personId},
            success:function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Please register your face',
                    showConfirmButton: true
                })
            }
        });
    }
});

$("#newTablePersonList").on("click", ".delete", function(){
    code = $(this).attr('code');
    empname = $(this).attr('empname');
    $("#facial_name").html("<b>" + empname + "</b>");
    $("#deletePerson").val(code);
    $("#deletemodal").modal();
});

$("#delete").click(function(){
    var msg = '';
    personId = $("#deletePerson").val();
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/deletePerson')?>",
        data: {personId:personId, serial_number:serial_number},
        success:function(response){
            if (response) {
                var taskData = '"pass":"12345678",id:"'+ personId +'"';
                var interface = 'person/delete';
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('facial_/saveTaskToDevice')?>",
                    data: {serial_number:serial_number,interface:interface,task:taskData,ip:ip},
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
                setTimeout(function() {
                    loadFacialSetupPerson();
                }, 1000);
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error Please Coordinate With Developer!',
                    timer: 1500
                })
            }
        }
    });
});

</script>