<table class="table table-striped table-bordered table-hover" id="preRequirementsTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary preaddbtn" code=""><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th width='10%' class="align_center"><b>Actions</b></th>
            <!-- <th><b>Code</b></th> -->
            <th><b>Description</b></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center">
                <a code="<?=$row['code']?>" desc="<?=$row['description']?>" class="btn btn-info preeditbtn" href="#modal-view" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?=$row['code']?>" desc="<?=$row['description']?>"  class="btn btn-danger predelbtn"><i class="glyphicon glyphicon-trash"></i></a>
            </td>
            <!-- <td><?=$row['code']?></td> -->
            <td><?=$row['description']?></td>
        </tr>
        <?php endforeach ?>
    </tbody>
    
</table>
<div id="deletemodals" class="modal fade" role="dialog">
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
        <center><b><h3 tag="title" class="modal-title">Delete Pre Requirement</h3></b></center>
          </div>
          <div class="modal-body align_center">
            <p>Are you sure you want to Remove <span id="pre_requirements_code"></span> from Pre Requirement Setup?</p>
          </div>
          <div class="modal-footer">
            <button type="button" id="deletes" class="btn btn-danger" data-dismiss="modal">Yes</button>
            <button type="button" class="btn btn-success" data-dismiss="modal">No</button>
          </div>
        </div>
        
    </div>
</div>
<script>
    var toks = hex_sha512(" ");
    $(".preaddbtn, .preeditbtn").click(function(){
        var code = '';
        code = $(this).attr('code');
        $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/managePreRequirements')?>",
            data: {
              toks:toks,
              code:GibberishAES.enc(code, toks)
            },
            success:function(response){
                $("#myModal").modal();
                $("#myModal").html(response);
                preRequirements_setup();
            }
        });
    });

    $("#preRequirementsTable").on("click", ".predelbtn", function(){
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

            code = $(this).attr("code");
            $.ajax({
                type: "POST",
                url: "<?= site_url('setup_/deletePreRequirements')?>",
                data: {
                  toks: toks,
                  code:GibberishAES.enc(code, toks)
                },
                success:function(response){
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Requirements has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                    preRequirements_setup();
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

    $(document).ready(function(){
        var table = $('#preRequirementsTable').DataTable({
        });
        new $.fn.dataTable.FixedHeader( table );
    });

    if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");

</script>