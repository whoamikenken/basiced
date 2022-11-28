<table class="table table-striped table-bordered table-hover" id="tables" >
    <thead>
        <tr>
            <th><button class="btn btn-primary" id="addnew"><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New</span></button></th>
        </tr>
        <tr>
            <th align="center">Rank</th>
            <th align="center">Name</th>
            <th align="center">Actions</th>
        </tr>
    </thead>
    <tbody id="employeelist" style="cursor: pointer;">
        <?php foreach($record as $row): 
            ?>
            <tr>
                <td><?=$row['rank']?></td>
                <td><?=$row['name']?></td>
                <td align="center">
                    <a code="<?=$row['id']?>" class="btn btn-info editbtn" href="#modal-view" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?=$row['id']?>" class="btn btn-danger delbtn"><i class="glyphicon glyphicon-trash"></i></a>
                </td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>
<script>
    $(document).ready(function(){
        var table = $('#tables').DataTable();
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#addnew").click(function () {
        var code = "none";
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + "/webcheckin_/manageSurveyCatSetup",
            data: {code: code},
            success: function (response) {
                $("#modal-view").modal();
                $("#modal-view").find("h3[tag='title']").text("Add New Setup");
                $("#modal-view").find("div[tag='display']").html(response);
            }
        });
    });

    $("#tables").delegate(".editbtn", "click", function() {
            $.ajax({
                url : $("#site_url").val() + "/webcheckin_/manageSurveyCatSetup",
                type: "POST",
                data: {code: $(this).attr("code")},
                success: function(msg){
                    $("#modal-view").modal();
                    $("#modal-view").find("h3[tag='title']").text("Edit Setup");
                    $("#modal-view").find("div[tag='display']").html(msg);
                }
            });
    });

    $("#tables").delegate(".delbtn", "click", function() {
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
            var uid = $(this).attr("userid");
             $.ajax({
                 url: $("#site_url").val() + "/webcheckin_/deleteSurveyCat",
                 data: {code: $(this).attr("code")},
                 type: "POST",
                 success: function(msg){
                    if (msg == "success") {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Category has been deleted successfully.',
                            showConfirmButton: true,
                            timer: 1000
                      })
                      setTimeout(function() {
                        surveyCatSetup();
                      }, 1500); 
                    }else{
                        swalWithBootstrapButtons.fire(
                          'Error',
                          'Please contact developer.',
                          'error'
                        )
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
    
</script>