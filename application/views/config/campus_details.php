
<table class="table table-striped table-bordered table-hover" id="campusTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn" code=""><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>Code</b></th>
            <th><b>Description</b></th>
            <th><b>Pricipal</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <a code="<?=$row['code']?>" class="btn btn-info editbtn" href="#modal-view" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?=$row['code']?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
            </td>
            <td><?=$row['code']?></td>
            <td><?=$row['description']?></td>
            <td><?=$this->extensions->getEmployeeName($row['campus_principal'])?></td>
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
                        <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b>
                        </h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                    </div>
                </div>
                <center><b>
                        <h3 tag="title" class="modal-title">Delete Campus</h3>
                    </b></center>
            </div>
            <div class="modal-body" style="text-align: center;">
                <p>Are you sure you want to Remove <span id="campus_code"></span> from Campus Setup?</p>
            </div>
            <div class="modal-footer">
                <button type="button" id="delete" class="btn btn-danger" data-dismiss="modal">Yes</button>
                <button type="button" class="btn btn-success" data-dismiss="modal">No</button>
            </div>
        </div>

    </div>
</div>
<script>  
    $(".addbtn, .editbtn").click(function () {
        var code = '';
        code = $(this).attr('code');
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/manageCampus')?>",
            data: {
                code: code
            },
            success: function (response) {
                $("#myModal").modal();
                $("#myModal").html(response);
                campus_setup();
            }
        });
    });

    $(".delbtn").click(function () {
        code = $(this).attr('code');
        $("#campus_code").html("<b>" + code + "</b>");
        $("#deletemodal").modal();
    });

    $("#delete").click(function () {
        var code = '';
        var msg = '';
        code = $("#campus_code").text();
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/deleteCampus')?>",
            data: {
                code: code
            },
            success: function (response) {
                if (response) msg = "Successfully Deleted! ";
                else msg = "Failed to Delete. Employee already assigned in this campus.";
                alert(msg);
                campus_setup();
            }
        });
    });

    $(document).ready(function () {
        var table = $('#campusTable').DataTable({
        });
        new $.fn.dataTable.FixedHeader(table);
    });

    if ("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");

</script>