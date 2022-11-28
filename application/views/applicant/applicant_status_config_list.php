<?php

 /**
 * @author Max Consul
 * @copyright 2018
 */

?>


<table class="table table-striped table-bordered table-hover" id="applicantStatTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn-approval"><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th class='align_center' width='10%'><b>Actions</b></th>
            <th class='align_center'><b>Teaching Type</b></th>
            <th class='align_center'><b>Description</b></th>
            <th class='align_center'><b>Message</b></th>
            <th class='align_center'><b>Sequence</b></th>
            <th class='align_center'><b>For Email</b></th>
        </tr>
    </thead>

    <tbody> 
    <?php if($data){ ?>
          <?php foreach($data as $value): ?>
            <tr>
               <td class="align_center">
                    <a id="<?=$value['id'] ?>" class="btn btn-info editbtn-status"><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                    <a id="<?=$value['id'] ?>" class="btn btn-danger delbtn-status"><i class="glyphicon glyphicon-trash"></i></a>
                </td>
                <td class='align_center'><?= ($value['type'] == 'teaching' ? 'Teaching' : 'Non-Teaching' ) ?></td>
                <td class='align_center'><?= $value['description']?></td>
                <td class='align_left'><?= $value['message']?></td>
                <td class='align_center'><?= $value['seqno']?></td>
                <td class='align_center'><?= ($value['foremail']) ? "YES": "NO" ?></td>
            </tr>
          <?php endforeach ?>
    <?php } ?>
    </tbody>
    
</table>

<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
    var table = $('#applicantStatTable').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

    $(".addbtn-approval").click(function(){
        $.ajax({
            url:"<?= site_url('applicant/loadApplicantStatusDetail') ?>",
            success:function(response){
                $("#datamodal_stat").html(response);
                $("#datamodal_stat").modal('toggle');
            }
        });
    });

    $("#applicantStatTable tbody").delegate(".editbtn-status", "click", function(){
        var id = $(this).attr('id');
        if(id){
            $.ajax({
                type:"POST",
                url: "<?= site_url('applicant/manageApplicantStatus') ?>",
                data: {id: GibberishAES.enc( id, toks), tag: GibberishAES.enc( "edit", toks), toks:toks},
                success:function(response){
                    $("#datamodal_stat").html(response);
                    $("#datamodal_stat").modal('toggle');
                }
            });
        }
    });

    $("#applicantStatTable tbody").delegate(".delbtn-status", "click", function(){
        var id = $(this).attr("id");
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
            $.ajax({
            url: "<?= site_url('applicant/deleteApprovalStatus') ?>",
            type: "POST",
            data: {id:  GibberishAES.enc( $(this).attr("id"), toks), toks:toks},
            success:function(response){
                // location.reload();
                loadApplicantStatus();
                if(response){
                    Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: "Successfully deleted approval status.",
                          showConfirmButton: true,
                          timer: 1000
                      })
                }
                else{
                    Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: "Failed to delete approval status.",
                          showConfirmButton: true,
                          timer: 1000
                      })
                }
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

    $("#delete_status").click(function(){
        $.ajax({
            url: "<?= site_url('applicant/deleteApprovalStatus') ?>",
            type: "POST",
            data: {id: $(this).attr("tag")},
            success:function(response){
                location.reload();
                if(response){
                    Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: "Approval Status has been deleted successfully.",
                          showConfirmButton: true,
                          timer: 1000
                      })
                }
                else{
                    Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: "Failed to delete approval status.",
                          showConfirmButton: true,
                          timer: 1000
                      })
                }
            }
        });
    });

    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");

</script>