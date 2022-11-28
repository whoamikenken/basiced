<?php

 /**
 * @author Max Consul
 * @copyright 2019
 */
 
?>

<table class="table table-striped table-bordered table-hover table-condensed" id="nationalityDetails">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn"><span class="glyphicon glyphicon-plus-sign"></span><span style="font-family: Tahoma;"> Add New </span></a>
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
                <a id="<?=$row['nationalityid']?>" class="btn btn-info editbtn" href="#modal-view" ><span class="glyphicon glyphicon-edit"></span></a>&nbsp;&nbsp;<a id="<?=$row['nationalityid']?>" desc="<?=$row['description']?>" class="btn btn-danger delbtn"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
            <td><?=$row['nationalityid']?></td>
            <td><?=$row['description']?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
    
</table>
<div id="deletemodal" class="modal fade" role="dialog">
    <div class="modal-dialog">

        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal">&times;</button>
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                        <h5>D`Great</h5>
                    </div>
                </div>
                <center><h4 class="modal-title">Delete Nationality Setup</h4></center>
            </div>
            <div class="modal-body">
                <h5 class="align_center">Are you sure you want to Remove <span id="nationalityDesc"></span> from Nationality Setup?</h5>
            </div>
            <div class="modal-footer">
                <button type="button" id="delete" class="btn btn-success" data-dismiss="modal">Yes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </div>
        
    </div>
</div>
<input type="hidden" id="tableCode" value="code_nationality">
<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
    var table = $('#nationalityDetails').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

    $("#nationalityDetails").on("click", ".addbtn, .editbtn", function(){
        var code = $(this).attr('id');
        var tableCode = $("#tableCode").val();
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/manageHRSetup')?>",
            data: {code: GibberishAES.enc( code, toks), tableCode: GibberishAES.enc( tableCode, toks), toks:toks},
            success:function(response){
                $("#modal-view").modal();
                $("#modal-view").find("h3[tag='title']").text(code ? "Edit Nationality Setup" : "Add Nationality Setup ");
                $("#modal-view").find("div[tag='display']").html(response);
                nationalitySetup();
            }
        });
    });

    $("#nationalityDetails").on("click", ".delbtn", function(){
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {

            var code = $(this).attr('id');
            var tableCode = $("#tableCode").val();
            $.ajax({
                type: "POST",
                url: "<?= site_url('setup_/deleteHRSetup')?>",
                data: {code: GibberishAES.enc( code, toks), tableCode: GibberishAES.enc( tableCode, toks), toks:toks},
                success:function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Nationality has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                    nationalitySetup();
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

    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");

</script>