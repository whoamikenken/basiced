<table class="table table-striped table-bordered table-hover" id="deviceTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn" action="add" code=""><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>Serial Number</b></th>
            <th><b>Name</b></th>
            <th><b>I.P Adress</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <a code="<?=$row['serial_number']?>" class="btn btn-primary editbtn" action="add" href="#modal-view" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?=$row['serial_number']?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>&nbsp;&nbsp;<a code="<?=$row['serial_number']?>" class="btn btn-info history"><i class="glyphicon glyphicon-th"></i></a>
            </td>
            <td><?=$row['serial_number']?></td>
            <td><?=$row['name']?></td>
            <td><?=$row['ip']?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
    
</table>
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
            <p>Are you sure you want to Remove <span id="facial_name"></span> from Facial Device Setup?</p>
          </div>
          <div class="modal-footer">
            <button type="button" id="delete" class="btn btn-danger" data-dismiss="modal">Yes</button>
            <button type="button" class="btn btn-default" data-dismiss="modal">No</button>
          </div>
        </div>
        
    </div>
</div>
<script>

$(document).ready(function(){
    var table = $('#deviceTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

$(".addbtn, .editbtn").click(function(){
    var code = '';
    var action = '';
    code = $(this).attr('code');
    action = $(this).attr('action');
    $.ajax({
        type: "POST",
        url: "<?= site_url('setup_/manageFacial')?>",
        data: {code:code,action:action},
        success:function(response){
            $("#myModal").modal();
            $("#myModal").html(response);
            loadFacialSetup();
        }
    });
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

$(".history").click(function(){
    code = $(this).attr('code');
    loadFacialHistory(code);
});

function loadFacialHistory(code){
    $.ajax({
        type: "POST",
        url: "<?= site_url('setup_/loadFacialSetup')?>",
        data: {code:code},
        success:function(response){
            $("#history_table").html(response);
        }
    });
}

</script>