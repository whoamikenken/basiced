<table class="table table-striped table-bordered table-hover" id="deptDeficiencyTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary addBtnDept" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>
            </th>
        </tr>                            
        <tr style="background-color: #0072c6;">
            <th width='10%' class="align_center"><b>Actions</b></th>
            <th><b>#</b></th>
            <th><b>Office</b></th>
        </tr>
    </thead>
    <tbody>
        <?php 
        $idCounter = 1;
        foreach ($deparments as $value): ?>
            <tr>
                <td class="align_center"> 
                    <a id="<?=$value['id'];?>" deptid="<?=$value['deptid'];?>" class="btn btn-info editBtnDept" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i></a>
                    <a id="<?=$value['id'];?>" deptid="<?=$value['deptid'];?>" department="<?=$value['description'];?>" class="btn btn-danger delBtnDept"><i class="glyphicon glyphicon-trash"></i></a>
                </td>
                <td><?= $idCounter ?></td>
                <td><?= $value['description'] ?></td>
            </tr>
        <?php 
            $idCounter++;
            endforeach ?>
    </tbody>
</table>
<div id="delete-alerts" class="hide">
    <div class="align_center"><h5>Are You sure you want to delete <span id="chosen-rows" class="text-error"></span> ?</h5></div>
</div>
<div style="display: none;">
<div id="delete-alert-footers">
    <input type="hidden" class="hiddenid" />
    <a href="#" class="btn btn-success" id="delDeptid">Yes</a>
    <a href="#" class="btn btn-danger del-close" data-dismiss="modal">No</a>
</div>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        var table = $('#deptDeficiencyTable').DataTable({

        });
        new $.fn.dataTable.FixedHeader( table );
    }); 

    $(".addBtnDept,.editBtnDept").click(function(){
        $("#modal-view").find("#delete-alert-footer").html("");
        $("#modal-view").find("#delete-alert-footers").html("");
        $("#modal-view").find("#modalclose").show();
        $("#modal-view").find("#button_save_modal").show();
        var id = "";
        var deptid = "";
        if($(this).attr("id")) id = $(this).attr("id");
        if($(this).attr("deptid")) deptid = $(this).attr("deptid");
        $("#modal-view").find("h3[tag='title']").text(id ? "Edit Concerned Office" : "Add Concerned Office");
        $("#button_save_modal").text("Save");
        var form_data = {
            deptid: deptid,
            action: id
        };
        $.ajax({
            url: "<?=site_url('deficiency_/deptDeficiencyModal')?>",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#modal-view").find("div[tag='display']").html(msg);
            }
        });  
    });

    $("#button_save_modal").unbind().click(function(){
        var deptid = $("#deptidx").val();
        var id = $("#deptCode").val();
        $.ajax({
            url: "<?= site_url('deficiency_/saveDeficiencyDept') ?>",
            type: "POST",
            data: {deptid:deptid, id:id},
            success:function(){
                $("#modalclose").click();
                if(id){
                    Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Concerned department has been updated successfully.',
                          showConfirmButton: true,
                          timer: 1000
                      })
                }else{
                    Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Concerned department has been saved successfully.',
                          showConfirmButton: true,
                          timer: 1000
                      })
                }
                loadDeptDeficiency();
            }
        })
    })

    $(".delBtnDept").unbind().click(function(){
        var id = $(this).attr("id");
        var desc = $(this).attr("department");
        var deptid = $(this).attr("deptid");
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
            deleteDeficiencyDept(id,deptid);
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



    
</script>
