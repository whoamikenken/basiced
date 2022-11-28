<table class="table table-striped table-bordered table-hover" id="officeListTable">
    <thead>
        <tr>
            <th>
                <a class="btn btn-primary modalbtn" id="addoffice" href="#modal-view" data-toggle="modal"><span class="glyphicon glyphicon-plus"></span> Add New</a>
            </th>
        </tr>   
        <tr style="background-color: #0072c6;">
            <th class="align_center col-md-1"></th>
            <th class="sorting_asc">Code</th>
            <th>Office</th>
            <th>Department</th>
            <th>Department Head / Vice Principal</th>
            <th>Area coordinator / Immediate Supervisor</th>
        </tr>
    </thead>   
    <tbody>
        <?php foreach($records as $row): ?>
        <tr>
            <td class="align_center col-md-1">
                <a class="btn btn-info modalbtn" href="#modal-view" data-toggle="modal" code='<?=$row['code']?>'><span class="glyphicon glyphicon-edit"></span></a>&nbsp;
                <a class="btn btn-danger deletebtn" href="#" code='<?=$row['code']?>'><span class="glyphicon glyphicon-trash"></span></a>
            </td>
            <td class="align_center"><?=$row['code']?></td>
            <td><?=$row['description']?></td>
            <td><?=($row['department_id'])?$this->setup->getDepartmentDesc($row['department_id']):""?></td>
            <td><?=$this->employee->getfullname($row['head'])?></td>
            <td><?=$this->employee->getfullname($row['divisionhead'])?></td>
        </tr>
<?php endforeach ?>
    </tbody>
</table>
<script>
  var toks = hex_sha512(" ");
    $(document).ready(function(){
    var table = $('#officeListTable').DataTable({
    });
 
    new $.fn.dataTable.FixedHeader( table );
});

$("#officeListTable,#addoffice").on("click", ".modalbtn", function(){
    var code = "";
    var job = "add";  
    if($(this).attr("code")) code = $(this).attr("code");
    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Office" : "Add Office");
    if(code) job = 'edit'
    $("#button_save_modal").text("Save");    
    var form_data = {
        code:  GibberishAES.enc(code , toks),
        job:  GibberishAES.enc(job , toks),
        toks:toks
    };
    $.ajax({
        url: "<?=site_url('maintenance_/manage_office')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
        }
    });  
});

$("#officeListTable").on("click", ".deletebtn", function(){
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
                url:"<?=site_url("maintenance_/save_office")?>",
                type:"POST",
                data:{
                   code: GibberishAES.enc($(this).attr("code")  , toks),
                   job:GibberishAES.enc( "delete"  , toks),
                   toks:toks
                },
                success: function(msg){
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Office has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                      setTimeout(function() {
                        office_setup();
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