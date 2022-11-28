<?php

 /**
 * @author Max Consul
 * @copyright 2019
 */
 
?>

<table class="table table-striped table-bordered table-hover table-condensed" id="DepartmentDetails">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addbtn" id=""><span class="glyphicon glyphicon-plus-sign"></span><span style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>Code</b></th>
            <th><b>Description</b></th>
            <!-- <th><b>Principal</b></th> -->
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <?php
            $row = Globals::_array_XHEP($row);
        ?>
        <tr>
            <td class="align_center">
                <a id="<?=$row['code']?>" class="btn btn-info editbtn" href="#modal-view" ><span class="glyphicon glyphicon-edit"></span></a>&nbsp;&nbsp;<a id="<?=$row['code']?>" class="btn btn-danger delbtn"><span class="glyphicon glyphicon-trash"></span></a>
            </td>
            <td><?=$row['code']?></td>
            <td><?=$row['description']?></td>
            <!-- <td><?=($row['head']) ? $this->extensions->getEmployeeName($row['head']) : "<i>No principal assigned.</i>" ?></td> -->
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
                <center><h4 class="modal-title">Delete Manage Department</h4></center>
            </div>
            <div class="modal-body">
                <h5>Are you sure you want to Remove <span id="deptid"></span> from  Manage Department Setup?</h5>
            </div>
            <div class="modal-footer">
                <button type="button" id="delete" class="btn btn-success" data-dismiss="modal">Yes</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">No</button>
            </div>
        </div>
        
    </div>
</div>
<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
    var table = $('#DepartmentDetails').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});

    $("#DepartmentDetails").on("click", ".addbtn, .editbtn", function(){
        var code = '';
        code = $(this).attr('id');
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/manageDepartment')?>",
            data: {code:GibberishAES.enc(code, toks), toks:toks},
            success:function(response){
                $("#modal-view").modal();
                $("#modal-view").find("h3[tag='title']").text(code ? "Edit Department setup" : "Add Department Setup ");
                $("#modal-view").find("div[tag='display']").html(response);
                department_setup();
            }
        });
    });

    $("#DepartmentDetails").on("click", ".delbtn", function(){
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
            code = $(this).attr('id');
            $.ajax({
                type: "POST",
                url: "<?= site_url('setup_/deleteDepartment')?>",
                data: {code:GibberishAES.enc(code, toks), toks:toks},
                success:function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Department has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                      setTimeout(function() {
                        department_setup();
                      }, 1500); 
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