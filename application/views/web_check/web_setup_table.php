<table class="table table-striped table-bordered table-hover" id="tables" >
    <thead>
        <tr>
            <th><button class="btn btn-primary" id="addnew"><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New</span></button></th>
            <th><button class="btn btn-primary" id="editBatch"><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Adjust By Batch</span></button></th>
        </tr>
        <tr>
            <th>Employee ID</th>
            <th>Name</th>
            <th>Office</th>
            <th>Date From</th>
            <th>Date To</th>
            <th>Adjustment</th>
            <th>Status</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody id="employeelist" style="cursor: pointer;">
        <?php foreach($employee as $row): 
            ?>
            <tr>
                <td><?=Globals::_e($row['employee'])?></td>
                <td><?=Globals::_e($row['fullname'])?></td>
                <td><?=Globals::_e($row['officedesc'])?></td>
                <td><?=Globals::_e($row['date_from'])?></td>
                <td><?=Globals::_e($row['date_to'])?></td>
                <td align="center"><input type="checkbox" class="adjustmentBatch" value="0" idkey="<?=$row['id']?>" empkey="<?=$row['employee']?>"/></td>
                <td style="<?= ($row['status'] == "active")? "color:green":"color:red" ?>"><?=Globals::_e($row['status'])?></td>
                <td align="center">
                    <a code="<?=$row['id']?>" class="btn btn-info editbtn" href="#modal-view" ><i class="glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;<a code="<?=$row['id']?>" class="btn btn-danger delbtn" style="<?= ($row['log_type'] == "")? "":"display: none" ?>"><i class="glyphicon glyphicon-trash"></i></a>
                </td>
            </tr>
        <?php endforeach ?>  
    </tbody>
</table>
<script>
    var toks = hex_sha512(" ");
    $(document).ready(function(){
        var table = $('#tables').DataTable();
        new $.fn.dataTable.FixedHeader( table );
    });

    $("#addnew").click(function () {
        var code = "none";
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + "/webcheckin_/manageWebSetup",
            data: {code: GibberishAES.enc(code, toks), type:GibberishAES.enc("add", toks), toks:toks},
            success: function (response) {
                $("#modal-view").modal();
                $("#modal-view").find("h3[tag='title']").text("Add New Setup");
                $("#modal-view").find("div[tag='display']").html(response);
            }
        });
    });

    $("#editBatch").click(function () {
        var code = emp = "";
        $('.adjustmentBatch').each(function(i, obj) {
            if ($(this).is(":checked")) {
              code = code+"/"+$(this).attr('idkey');
              emp = emp+"/"+$(this).attr('empkey');
            }
        });
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + "/webcheckin_/manageWebSetup",
            data: {code: GibberishAES.enc(code.substring(1), toks),employee: GibberishAES.enc(emp.substring(1), toks), type:GibberishAES.enc("batch", toks), toks:toks},
            success: function (response) {
                $("#modal-view").modal();
                $("#modal-view").find("h3[tag='title']").text("Batch Date Range Adjustment");
                $("#modal-view").find("div[tag='display']").html(response);
            }
        });
    });

    $("#tables").delegate(".editbtn", "click", function() {
            $.ajax({
                url : $("#site_url").val() + "/webcheckin_/manageWebSetup",
                type: "POST",
                data: {code: GibberishAES.enc($(this).attr("code"), toks),type:GibberishAES.enc("edit", toks), toks:toks},
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
                var id = $(this).attr("code");
                 $.ajax({
                     url:"<?=site_url("webcheckin_/deleteWebCheckInSetup")?>",
                     data: {id: GibberishAES.enc(id , toks),toks:toks},
                     type: "POST",
                     success: function(msg){
                      Swal.fire({
                            icon: 'success',
                            title: 'Deleted!',
                            text: 'Setup has been deleted successfully.',
                            showConfirmButton: true,
                            timer: 1000
                      })
                      setTimeout(function() {
                        webSetup();
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
    
</script>