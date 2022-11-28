<?php

 /**
 * @author Max Consul
 * @copyright 2019
 */
 
?>

<style>
    .table{
        width: 100% !important;
    }
</style>
<div class="manageRankContainter">
    <table class="table table-striped table-bordered table-hover" id="rank_table">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn" id=""><i class="icon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6">
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>Type</b></th>
            <th><b>Rank</b></th>
            <th><b>Set</b></th>
            <th><b>Basic Rate</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <a id="<?=$row['id']?>" class="btn btn-info editbtn" href="#modal-view" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;
                <a id="<?=$row['id']?>" type="<?=$row['type_desc']?>" rank="<?=$row['rank_desc']?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
            </td>
            <td><?=$row['type_desc']?></td>
            <td><?=$row['rank_desc']?></td>
            <td><?=$row['set_desc']?></td>
            <td><?=$row['basic_rate']?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
    
</table>
<!-- <div id="deletemodal" class="modal fade" role="dialog">
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
            <center><b><h3 tag="title" class="modal-title" id="tag">Delete Rank Table</h3></b></center>
          </div>
          <div class="modal-body align_center" >
            <p>Are you sure you want to Remove <span id="managerank_id"></span> from Rank Table?</p>
            
          </div>
          <div class="modal-footer">
            <button type="button" id="delete" class="btn btn-danger" data-dismiss="modal">Yes</button>
            <button type="button" class="btn btn-success" data-dismiss="modal">No</button>
            
        </div>
        </div>
        
    </div>
</div> -->

</div>

<script>
    var toks = hex_sha512(" ");
    $(document).ready(function(){
        $("#rank_table").dataTable({
            "pagination": "number",
            "oLanguage": {
                             "sEmptyTable":     "No Data Available.."
                         },
            "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "pageLength": 10,
            "scrollY": false,
            "scrollX": true
        });
    });


    $(".addbtn, .editbtn").click(function(){
        var id = '';
        id = $(this).attr('id');
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/manageRank')?>",
            data: {id:GibberishAES.enc(id, toks),toks:toks},
            success:function(response){
                $("#myModal").modal();
                $("#myModal").html(response);
            }
        });
    });

    $(".delbtn").click(function(){
        
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
                    id = $(this).attr('id');
                    
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('setup_/deleteManageRank')?>",
                        data: {id:GibberishAES.enc(id, toks),toks:toks},
                        success:function(response){
                            Swal.fire({
                              icon: 'success',
                              title: 'Success!',
                              text: 'Rank has been deleted successfully.',
                              showConfirmButton: true,
                              timer: 1000
                          })
                            managerank_setup();
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