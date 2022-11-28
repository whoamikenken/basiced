<style type="text/css">
    .swal2-cancel{
    margin-right: 20px;
}
</style>
<table class="table table-striped table-bordered table-hover" id="requestTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn" data-toggle="modal" href="#dtr-modal" request_code=""><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>Code</b></th>
            <th><b>Description</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <a request_code="<?=Globals::_e($row['request_code'])?>" id="<?= Globals::_e($row['id']) ?>" class="btn btn-info editbtn" data-toggle="modal" href="#dtr-modal" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a request_code="<?=Globals::_e($row['request_code'])?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
            </td>
            <td><?=Globals::_e($row['request_code'])?></td>
            <td><?=Globals::_e($row['description'])?></td>
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
        <center><b><h3 tag="title" class="modal-title">Delete Remark Setup</h3></b></center>
          </div>
          <div class="modal-body align_center" >
            <p>Are you sure you want to Remove <span id="request_request_code"></span> from Remark Setup?</p>
          </div>
          <div class="modal-footer">
            <button type="button" id="delete" class="btn btn-danger" data-dismiss="modal">Yes</button>
            <button type="button" class="btn btn-success" data-dismiss="modal">No</button>
          </div>
        </div>
        
    </div>
</div>
<script>
    var toks = hex_sha512(" "); 
    validateCanWrite();
    $(".addbtn, .editbtn").click(function(){
        var request_code = '';
        request_code = $(this).attr('request_code');
        id = $(this).attr('id');
        var form_data = {
            toks: toks,
            request_code: GibberishAES.enc(request_code, toks),
            id: GibberishAES.enc(id, toks)
        }
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/manageRequest')?>",
            data: form_data,
            success:function(response){
                 $("#dtr-modal").find("h3[tag='title']").text("");
                $("#dtr-modal").find("div[tag='display']").html(response);
                request_setup();
            }
        });
    });

    $(".delbtn").click(function(){
        request_code = GibberishAES.enc($(this).attr('request_code'), toks)
        // $("#request_request_code").html("<b>" + request_code + "</b>");
        // $("#deletemodal").modal();
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure ?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            var msg = '';
            $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/deleteRequest')?>",
            data: {request_code:request_code, toks:toks},
            dataType: "JSON",
            success:function(response){
                if(response.err_message == 0){
                    msg = "Remark has been deleted successfully.";
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    })
                } 
                else{
                    msg = "This remark cannot be deleted because it is already used " +response.Count+ " times."
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    })
                };
                // alert(msg);
                    
                request_setup();
            }
        });
          } else if (
            result.dismiss === Swal.DismissReason.cancel
          ) {
            swalWithBootstrapButtons.fire(
              'Cancelled',
              'Data is safe.',
              'error'
            )
          }
        })
    });
    $("#delete").click(function(){
        var request_code = '';
        var msg = '';
        request_code = $("#request_request_code").text();
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/deleteRequest')?>",
            data: {request_code:request_code},
            dataType: "JSON",
            success:function(response){
                if(response.err_message == 0){
                    msg = "Successfully Deleted! ";
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    })
                } 
                else{
                    msg = "This remarks cannot be deleted. The remarks is used "+response.Count+" times."
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: msg,
                        showConfirmButton: true,
                        timer: 2000
                    })
                };
                // alert(msg);
                    
                request_setup();
            }
        });
    });

    function validateCanWrite(){
        if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
        else $(".btn").css("pointer-events", "");
    }

$(document).ready(function(){
    var table = $('#requestTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

</script>