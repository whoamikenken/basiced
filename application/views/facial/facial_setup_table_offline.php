<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
// echo "<pre>";print_r($this->session->userdata('username'));die;
?>
<table class="table table-striped table-bordered table-hover" id="MasterTable">
    <thead>                          
        <tr >
            <th class="align_center"><b>Actions</b></th>
            <th><b>Device I.D</b></th>
            <th><b>Name</b></th>
            <th><b>Person Count</b></th>
            <th><b>Face Count</b></th>
            <th><b>Device Version</b></th>
            <th><b>I.P</b></th>
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
                <a code="<?= $row->deviceKey ?>" ip="<?= $row->ip ?>" devicename="<?php echo $row->deviceName ?>" class="btn btn-info logs" data-toggle="tooltip" data-placement="top" title="Device User Logs"><i class="glyphicon glyphicon-th-list"></i></a>&nbsp;&nbsp;
                <a ip="<?= $row->ip ?>" code="<?= $row->deviceKey ?>" class="btn btn-info resynclogs"><i class="glyphicon glyphicon-list-alt"></i>&nbsp;Import Logs</a>
            </td>
            <td><?= $row->deviceKey ?></td>
            <td><?= $row->deviceName ?></td>
            <td><?= $row->personCount ?></td>
            <td><?= $row->faceCount ?></td>
            <td><?= $row->version ?></td>
            <td><?= $row->ip ?></td>
            <td><?= date("Y-m-d H:i:s", substr($row->time, 0, -3)); ?></td>
            <td><?= ($since_start->i > 3 || $since_start->days > 0 || $since_start->h > 0)? "<h5 style='color:red'>DISCONNECTED</h5>":"<h5 style='color:green'>CONNECTED</h5>" ?></td>
        </tr>
        <?php endforeach ?>
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
        <center><b><h3 tag="title" class="modal-title">Delete Facial Device</h3></b></center>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to Remove <span id="facial_name"></span> from Facial Device Setup?</p>
          </div>
          <div class="modal-footer">
            <button type="button" id="delete" class="btn btn-danger" data-dismiss="modal">Yes</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
          </div>
        </div>
        
    </div>
</div>
<div id="permission" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
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
        <center><b><h3 tag="title" class="modal-title">Facial Device Information</h3></b></center>
          </div>
          <div class="modal-body">
            <div class="form-group">
                <label for="serial_number">Device I.D</label>
                <input class="form-control" id="deviceKeySetting" type="text" disabled/>
            </div>
            <div class="form-group">
                <label for="serial_number">Name</label>
                <input class="form-control" id="nameSetting" type="text" value=""/>
            </div>
            <br><br>
            <div class="col-md-12" style="text-align: center;">
                <div class="col-md-2"></div>
                <div class="col-md-4"><a class="btn btn-warning deleteDeviceRecord" style="float: right;"><span class="" style="font-family: Tahoma;">Delete IN/OUT Device History</span></a></div>
                <div class="col-md-4" ><a class="btn btn-primary deviceTask" style="<?= ($this->session->userdata('username') == "pinnacle")? '':'display: none;'?>"><span class="" style="font-family: Tahoma;">View Task</span></a></div>
                <div class="col-md-4"><a class="btn btn-primary employeeBatch" data-dismiss="modal" type="button" style="float: left;"><span class="" style="font-family: Tahoma;">Add Employee(Batch)</span></a></div>
                <div class="col-md-2"></div>
            </div>
            <div class="col-md-12" id="taskTableView">
                
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
          </div>
        </div>
    </div>
</div>
<div id="EmplyoeeBatch" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg">

        <!-- Modal content-->
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
        <center><b><h3 tag="title" class="modal-title">Facial Device Setting</h3></b></center>
          </div>
          <div class="modal-body">
            <form id="BatchFilterForm">
                <input type="hidden" name="code" id="codeBatch" value="">
                <div class="form-group">
                    <label for="serial_number">Status</label>
                    <select class="chosen-batch" name="status" id="statusBatch">
                        <option value="">All Status</option>
                        <option value="1">Active</option>
                        <option value="0">In-Active</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="serial_number">Department</label>
                    <select class="chosen-batch" name="deptid" id="deptidBatch"><?=$this->extras->getDeptpartment()?></select>
                </div>
                <div class="form-group">
                    <label for="serial_number">Office</label>
                    <select class="chosen-batch" name="office" id="officeBatch"><?=$this->extras->getOffice()?></select>
                </div>
                <div class="form-group">
                    <label for="exampleInputName2">Employee</label>
                    <select class="chosen form-control" name="employeeFilter" id="employeeFilterbatch" multiple>
                    </select>
                </div>
            </form>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="button" id="employeeByBatch" class="btn btn-primary" data-dismiss="modal">Add</button>
            
          </div>
        </div>
    </div>
</div>
<script>
var toks = hex_sha512(" "); 
$(document).ready(function(){
    var table = $('#MasterTable').DataTable();
    new $.fn.dataTable.FixedHeader( table );
    $("#tableLogs").hide();
    employeeList()
    $('[data-toggle="tooltip"]').tooltip()
});

$(".deviceTask").click(function(){
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/loadFacialDeviceTask')?>",
        data: {serial_number:$("#deviceKeySetting").val()},
        success:function(response){
            $("#tableLogs").show();
            $("#taskTableView").html(response);
        }
    });
});


$(".employeeBatch").click(function(){
    $("#codeBatch").val($("#deviceKeySetting").val());
    $("#EmplyoeeBatch").modal();
});

$("#employeeByBatch").click(function(){
    var formdata = $("#BatchFilterForm").serialize()+ "&employeeList="+ $("#employeeFilterbatch").val();
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/syncEmployeeCount')?>",
        data: formdata,
        success:function(response){
            $.ajax({
                type: "POST",
                url: "<?= site_url('facial_/syncEmployee')?>",
                data: formdata,
                success:function(response){
                    setTimeout(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response+' employees added.',
                            showConfirmButton: true,
                            timer: 1000
                        })
                    }, 1500);
                    setTimeout(function() { location.reload(); }, 1500);
                }
            });
            let timerInterval
            Swal.fire({
              title: 'Alert will be close',
              html: 'Adding Employee Please Wait <b></b> seconds',
              timer: response,
              timerProgressBar: true,
              onBeforeOpen: () => {
                Swal.showLoading()
                timerInterval = setInterval(() => {
                  const content = Swal.getContent()
                  if (content) {
                    const b = content.querySelector('b')
                    if (b) {
                      b.textContent = Swal.getTimerLeft() / 1000
                    }
                  }
                }, 100)
              },
              onClose: () => {
                clearInterval(timerInterval)
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {

              }
            })
        }
    });
});


$("#MasterTable").on("click", ".setting", function(){
    $("#deviceKeySetting").val($(this).attr('code'));
    $("#nameSetting").val($(this).attr('codename'));
    $("#permission").modal();
});

$("#MasterTable").on("click", ".resetPerson", function(){
    var DeviceCode = $(this).attr('code'); 

    var taskData = '"pass":"12345678","id":"-1"';
    var interface = 'person/delete';
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/saveTaskToDevice')?>",
        data: {serial_number:DeviceCode,interface:interface,task:taskData},
        success:function(response){
            $.ajax({
                type: "POST",
                url: "<?= site_url('facial_/resetDevicePerson')?>",
                data: {code:DeviceCode},
                success:function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Reset',
                        showConfirmButton: true,
                        timer: 1000
                    })
                }
            });
        }
    });
});

$("#MasterTable").on("click", ".remove", function(){
    var DeviceCode = $(this).attr('code'); 
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/removeDevice')?>",
        data: {serial_number:DeviceCode},
        success:function(response){
           
        }
    });
});

$("#MasterTable").on("click", ".syncEmployeeNew", function(){
    var miliseconds; 
    var DeviceCode = $(this).attr('code'); 
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/syncEmployeeCountDevice')?>",
        data: {},
        success:function(response){
            $.ajax({
                type: "POST",
                url: "<?= site_url('facial_/syncEmployeeRA')?>",
                data: {code:DeviceCode, status:1, sync:'all'},
                success:function(response){
                    setTimeout(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response+' employees added.',
                            showConfirmButton: true,
                            timer: 1000
                        })

                        // setTimeout(function() { location.reload(); }, 1500);
                    }, 1500);
                }
            });
            let timerInterval
            Swal.fire({
              title: 'Alert will be close',
              html: 'Syncing Please Wait <b></b> seconds',
              timer: response,
              timerProgressBar: true,
              allowOutsideClick: false,
              onBeforeOpen: () => {
                Swal.showLoading()
                timerInterval = setInterval(() => {
                  const content = Swal.getContent()
                  if (content) {
                    const b = content.querySelector('b')
                    if (b) {
                      b.textContent =  Math.trunc(Swal.getTimerLeft() / 1000)
                    }
                  }
                }, 100)
              },
              onClose: () => {
                clearInterval(timerInterval)
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {

              }
            })
        }
    });
});

$("#MasterTable").on("click", ".syncEmployee", function(){
    var miliseconds; 
    var DeviceCode = $(this).attr('code'); 
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/syncEmployeeCountDevice')?>",
        data: {},
        success:function(response){
            $.ajax({
                type: "POST",
                url: "<?= site_url('facial_/syncEmployee')?>",
                data: {code:DeviceCode, status:1, sync:'all'},
                success:function(response){
                    setTimeout(function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: response+' employees added.',
                            showConfirmButton: true,
                            timer: 1000
                        })

                        // setTimeout(function() { location.reload(); }, 1500);
                    }, 1500);
                }
            });
            let timerInterval
            Swal.fire({
              title: 'Alert will be close',
              html: 'Syncing Please Wait <b></b> seconds',
              timer: response,
              timerProgressBar: true,
              allowOutsideClick: false,
              onBeforeOpen: () => {
                Swal.showLoading()
                timerInterval = setInterval(() => {
                  const content = Swal.getContent()
                  if (content) {
                    const b = content.querySelector('b')
                    if (b) {
                      b.textContent =  Math.trunc(Swal.getTimerLeft() / 1000)
                    }
                  }
                }, 100)
              },
              onClose: () => {
                clearInterval(timerInterval)
              }
            }).then((result) => {
              /* Read more about handling dismissals below */
              if (result.dismiss === Swal.DismissReason.timer) {

              }
            })
        }
    });
});

$("#MasterTable").on("click", ".editbtn", function(){
    // var code = '';
    // var action = '';
    // code = $(this).attr('code');
    // $.ajax({
    //     type: "POST",
    //     url: "<?= site_url('facial_/manageFacialLocal')?>",
    //     data: {code:code},
    //     success:function(response){
    //         $("#modal-view").find("div[tag='display']").html(response);
    //         $("#modal-view").find(".modal-title").html("Facial Device Setting");
    //         $("#modal-view").modal();
    //         loadFacialSetup();
    //     }
    // });

    var code = '';
    var action = '';
    var ip = '';
    code = $(this).attr('code');
    ip = $(this).attr('ip');
    $.ajax({
        type: "GET",
        crossDomain: true,
        url: "http://"+ip+":8090/getDeviceKey",
        data: {},
        success:function(response){
            console.log(response);
        }
    });


});

$("#MasterTable").on("click", ".history", function(){
    code = $(this).attr('code');
    ip = $(this).attr('ip');
    $("#setupTable").html("<h4><b>"+code+"</b></h4>");
    loadFacialDevice(code,ip);
});

$("#MasterTable").on("click", ".logs", function(){
    code = $(this).attr('code');
    devicename = $(this).attr('devicename');
    ip = $(this).attr('ip');
    $("#setupTable").html("<h4><b>"+devicename+" Facial Devices Logs</b></h4>");
    loadFacialDeviceLogs(code,ip);
});

$(".delbtn").click(function(){
    code = $(this).attr('code');
    $("#facial_name").html("<b>" + code + "</b>");
    $("#deletemodal").modal();
});

$("#delete").click(function(){
    var code = '';
    var msg = '';
    code = $("#facial_name").text();
    $.ajax({
        type: "POST",
        url: "<?= site_url('setup_/deleteFacial')?>",
        data: {code:code},
        success:function(response){
            if(response) msg = "Successfully Deleted! ";
            else msg = "Failed to Delete.";
            alert(msg);
            loadFacialSetup();
        }
    });
});

$("#MasterTable").on("click", ".resynclogs", function(){
    var code = '';
    var action = '';
    code = $(this).attr('code');
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/manageFacialLogsLocal')?>",
        data: {code:code},
        success:function(response){
            $("#modal-view").find("div[tag='display']").html(response);
            $("#modal-view").find(".modal-title").html("Facial Device Logs Import");
            $("#button_save_modal").text("Sync");
            $("#modal-view").modal();
            loadFacialSetup();
        }
    });
});

$("#Save").unbind('click').bind('click', function(event) {
    var deviceKey   =   $("#employeeid").attr("deviceKey");
    var name   =   $("#nameSetting").val();

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
       data     :   {deviceKey:deviceKey, name:name},
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
            loadFacialSetup()
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
                text: 'Records Deleted.',
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
            loadFacialSetup();
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
            loadFacialSetup();
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Records Deleted.',
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
        text: 'Device Records Deleted.',
        showConfirmButton: true,
        timer: 1500
    })
    loadFacialSetup();
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

function loadFacialDevice(code,ip){
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/loadFacialDevice')?>",
        data: {code:code,ip:ip},
        success:function(response){
            $("#data_table").html(response);
        }
    });
}

function loadFacialDeviceLogs(code,ip){
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/loadFacialDeviceLogsLocal')?>",
        data: {code:code,ip:ip},
        success:function(response){
            $("#data_table").html(response);
        }
    });
}

function employeeList(type, selected){
    $.ajax({
        url: "<?= site_url('webcheckin_/loadEmployeeListDropdownm')?>",
        type: "POST",
        data:{deptid: GibberishAES.enc($("#deptidBatch").val(), toks), office: GibberishAES.enc($("#officeBatch").val(), toks), status: GibberishAES.enc($("#statusBatch").val(), toks), type: GibberishAES.enc(type, toks),selected: GibberishAES.enc(selected, toks), toks:toks },
        success:function(response){
            $("#employeeFilterbatch").html(response);
            $("#employeeFilterbatch").trigger("chosen:updated");
        }
    });
}

$(".chosen-batch").chosen();
$("#employeeFilterbatch").chosen();
$(".chosen-batch").change(function(){
    employeeList("", "select");
});
</script>