
 <table class="table table-striped table-bordered table-hover" id="tablesexcluded" >
    <thead>
        <tr style="background-color: #ffc72c;">
            <th align="center">Employee ID</th>
            <th align="center">Name</th>
            <th align="center">Campus</th>
            <th align="center">Teaching Type</th>
            <th align="center">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($bio as $row): 
            ?>
            <tr>
                <td><?=$row['employeeid']?></td>
                <td><?=$row['fullname']?></td>
                <td><?=$row['campusid']?></td>
                <td><?=$row['teachingtype']?></td>
                <td><button class="btn btn-danger deleteEx" employeeid='<?=$row['employeeid']?>'>Delete</button></td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>

<script>
    var toks = hex_sha512(" ");
    $(document).ready(function(){
        var table = $('#tablesexcluded').DataTable();
        new $.fn.dataTable.FixedHeader( table );
        $('.chosen').chosen();
    });

    $("#tablesexcluded").delegate(".deleteEx", "click", function() {
        if($(this).attr("employeeid")){
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
                var form_data = {
                toks: toks,
                code : GibberishAES.enc($(this).attr("employeeid"), toks)
                }; 
                $.ajax({
                    url : "<?= site_url('fingerprint_/removeToExcluded')?>",
                    type: "POST",
                    data: form_data,
                    success: function(msg){
                        if (msg) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: "Employee's exclude fingerprint has been deleted successfully.",
                            showConfirmButton: true,
                            timer: 1000
                        })
                        }else{
                            Swal.fire({
                              icon: 'error',
                              title: 'Oops...',
                              text: 'Your Error Please Coordinate With Developer!',
                              timer: 1500
                            })
                        }
                        getEmployeeExcluded();
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
        }
    });

</script>