<div class="panel">
<input type="hidden" name="serial_number" id="serial_number" value="<?= $serial ?>">
<input type="hidden" name="ip" id="ip" value="<?= $ip ?>">
   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Device</b></h4></div>
   <br>
    <button type="button" class="btn btn-primary" style="margin-left: 2%;" id="restart">Restart</button>
    <button type="button" class="btn btn-success" id="sync">Sync Device Log To Database</button>
    <button type="button" class="btn btn-danger" id="reset">Reset Device</button>
    <button type="button" class="btn btn-info log" code="today">View Today Logs</button>
    <button type="button" class="btn btn-info log" code="all">View Logs</button><br><br>
   <div class="panel-body" id="data_table">
        <table class="table table-striped table-bordered table-hover" id="deviceTable">
            <thead>
                <tr>
                    <th>
                        <a class="btn btn-primary addbtn" action="add" code=""><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;">Add New</span></a>
                    </th>
                </tr>                            
                <tr style="background-color: #0072c6;">
                    <th width='10%' class="align_center"><b>Actions</b></th>
                    <th><b>Person ID</b></th>
                    <th><b>Employee ID</b></th>
                    <th><b>Name</b></th>
                    <th><b>Card</b></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($records as $row): ?>
                <tr>
                    <td class="align_center">
                        <a code="<?= $row['id'] ?>" class="btn btn-primary editbtn" action="add" href="#modal-view" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?= $this->utils->getFullName($row['employeeid']) ?>" person="<?= $row['personId'] ?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;&nbsp;<a code="<?= $this->utils->getFullName($row['employeeid']) ?>" class="btn btn-info history"><i class="glyphicon glyphicon-th"></i></a>
                    </td>
                    <td><?= $row['personId'] ?></td>
                    <td><?= $row['employeeid'] ?></td>
                    <td><?= $this->utils->getFullName($row['employeeid']) ?></td>
                    <td><?=$row['card']?></td>
                </tr>
                <?php endforeach ?>
            </tbody>
            
        </table>
    </div>
</div>

<div id="deletemodal" class="modal fade" role="dialog">
    <div class="modal-dialog">

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
        <center><b><h3 tag="title" class="modal-title">Delete Facial Device</h3></b></center>
          </div>
          <div class="modal-body">
            <p>Are you sure you want to Remove <span id="facial_name"></span> ID <span id="person_id"></span> from Facial Device?</p>
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
var gate = $("#serial_number").val();
$(document).ready(function(){
    var table = $('#deviceTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

$("#restart").click(function(){
    $.ajax({
        type: "POST",
        url: 'http://'+ ip +':8090/restartDevice',
        data: {pass:"12345678"},
        success:function(response){
            res = JSON.parse(response);
            if (res.success) {
                alert("Successfully Restart");
            }else alert("Error Device Not Found");
        }
    });
});

$("#reset").click(function(){
    $.ajax({
        type: "POST",
        url: 'http://'+ ip +':8090/person/delete',
        data: {pass:"12345678",id:"-1"},
        success:function(response){
            res = JSON.parse(response);
            if (res.success) {
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('setup_/resetDevice')?>",
                    data: {serial_number:gate},
                    success:function(response){
                        if(response) msg = "Successfully Deleted! ";
                        else msg = "Failed to Delete.";
                        alert(msg);
                        facial_device(gate);
                    }
                });
            }else alert("Error Device Not Found");
        }
    });
});

$("#sync").click(function(){
    $.ajax({
        type: "POST",
        url: 'http://'+ ip +':8090/findRecords',
        data: {pass:"12345678",personId:-1,length:-1,index:0,startTime:0,endTime:0},
        success:function(response){
            res = JSON.parse(response);
            console.log(res.data.records);
        }
    });
});

$(".addbtn, .editbtn").click(function(){
    var code = '';
    var action = '';
    code = $(this).attr('code');
    device = $("#serial_number").val();
    action = $(this).attr('action');

    $('#modal-view').modal("show");
    $('#modal-view').find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
    if (action == "add") {
        $("#modal-view").find(".modal-title").html("Add Person To Device");
    }else $("#modal-view").find(".modal-title").html("Edit Person To Device");

    $.ajax({
        type: "POST",
        url: "<?= site_url('setup_/manageDevicePerson')?>",
        data: {code:code,action:action,device:device},
        success:function(response){
            $("#modal-view").find("div[tag='display']").html(response);
        }
    });
});

$(".delbtn").click(function(){
    code = $(this).attr('code');
    person = $(this).attr('person');
    $("#facial_name").html("<b>" + code + "</b>");
    $("#person_id").html("<b>" + person + "</b>");
    $("#deletemodal").modal();
});

$("#delete").click(function(){
    var person = '';
    var msg = '';
    person = $("#person_id").text();
    $.ajax({
        type: "POST",
        url: 'http://'+ ip +':8090/person/delete',
        data: {pass:"12345678", id:person},
        success:function(response){
            res = JSON.parse(response);
            if (res.success) {
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('setup_/deletePerson')?>",
                    data: {code:person,serial_number:gate},
                    success:function(response){
                        if(response) msg = "Successfully Deleted! ";
                        else msg = "Failed to Delete.";
                        alert(msg);
                        facial_device(gate);
                    }
                });
            }else alert("Error Device Not Found");
        }
    });
});

$(".log").click(function(){
    code = $(this).attr('code');
    loadFacialHistory(code);
});

function loadFacialHistory(code){
    $.ajax({
        type: "POST",
        url: "<?= site_url('setup_/loadFacialHistory')?>",
        data: {code:code,gate:gate},
        success:function(response){
            $("#data_table").html(response);
        }
    });
}
</script>