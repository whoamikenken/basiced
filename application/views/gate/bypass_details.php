<table class="table table-striped table-bordered table-hover" id="bypassTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn" code="" action="add" data-toggle="modal" href="#dtr-modal"><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>Campus</b></th>
            <th><b>Employee</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <a code="<?=$row['id']?>" class="btn btn-info editbtn" data-toggle="modal" href="#dtr-modal" action="edit"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?=$row['id']?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
            </td>
            <td>
                <?php foreach(explode(",", $row['code']) as $code):?>
                    <span><?=$this->extensions->getCampusDescription($code)?></span>
                <?php endforeach ?>
                </td>
            <td>
                <?php foreach(explode(",", $row['employee']) as $empid):?>
                    <span><?=$this->extensions->getEmployeeName($empid)?></span>
                <?php endforeach ?>
            </td>
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
                    <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
                        <h4 class="media-heading" ><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family:Avenir; margin-top: -1%;">D`Great</p>
                    </div>
                    </div>
                <center><b><h3 tag="title" class="modal-title">Delete Employee</h3></b></center>
                </div>
                <div class="modal-body" style="text-align: center;">
                    <p>Are you sure you want to remove this from Terminal Access Setup? <span id="bypass_code" style="visibility: hidden;"></span> </p>
                </div>
                <div class="modal-footer">
                    <button type="button" id="delete" class="btn btn-danger" data-dismiss="modal">Yes</button>
                    <button type="button" class="btn btn-success" data-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>
<script>
    validateCanWrite();
    $(".addbtn, .editbtn").click(function(){
        var code = '';
        code = $(this).attr('code');
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/manageBypass')?>",
            data: {id:code},
            success:function(response){
                $("#dtr-modal").find("h3[tag='title']").text("");
                $("#dtr-modal").find("div[tag='display']").html(response);
                bypass_setup();
            }
        });
    });

    $(".delbtn").click(function(){
        code = $(this).attr('code');
        $("#bypass_code").html("<b>" + code + "</b>");
        $("#deletemodal").modal();
    });

    $("#delete").click(function(){
        var code = '';
        var msg = '';
        code = $("#bypass_code").text();
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/deleteBypass')?>",
            data: {code:code},
            success:function(response){
                if(response) msg = "Successfully Deleted! ";
                else msg = "Failed to Delete employee.";
                alert(msg);
                bypass_setup();
            }
        });
    });

    function validateCanWrite(){
        if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
        else $(".btn").css("pointer-events", "");
    }

$(document).ready(function(){
    var table = $('#bypassTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

</script>