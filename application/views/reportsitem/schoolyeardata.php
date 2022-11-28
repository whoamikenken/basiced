<table class="table table-hover sytable" id="sytable">
    <thead >
        <tr>
            <th>
                <a class="btn btn-primary"  href="#modal-view" id="add_sy" data-toggle="modal" style="margin-bottom: 10px; "><i class="icon glyphicon glyphicon-plus-sign" style="margin-right: 5px;"></i><b>Add New</b></a>
            </th>
        </tr>
        <tr style="background-color: #0072c6;">
            <th class="col-md-3"><b>SCHOOL YEAR</b></th>
            <th class="col-md-3"><b>FROM</b></th>
            <th class="col-md-3"><b>TO</b></th>
            <th class="col-md-1">&nbsp;</th>
        </tr>
    </thead>
    <tbody>
    	<?
        if(count($eb_list)>0){
        	foreach ($eb_list as $key => $row) {
                $monthlist = Globals::monthList();
                ?>
                <tr> 
                    <td><?=$row->sy?></td>
                    <td><?=$monthlist[$row->month_from]?></td>
                    <td><?=$monthlist[$row->month_to]?></td>
                    <td >
                        <a class="btn btn-danger pull-right delete_sy" id="delete_sy"  data-toggle="modal" tbl_id="<?=$row->id?>"><i class="icon glyphicon glyphicon-trash"></i></a>
                        <a class="btn btn-info pull-right edit_sy" href="#modal-view"  data-toggle="modal" id="edit_sy" style="margin-right: 10px;" tbl_id="<?=$row->id?>"><i class="icon glyphicon glyphicon-edit"></i></a>
                    </td>
                </tr>
        	<?}
        }else{?>
            <tr>  
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
       <? }?>
    </tbody>
</table>
<script type="text/javascript">
    $(document).ready( function () {
        $('.sytable').DataTable().destroy();
        $(".sytable").dataTable({
            "sPaginationType": "full_numbers",
            "oLanguage": {
                             "sEmptyTable":     "No Data Available.."
                         },
            "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
            "columnDefs": [{ "orderable": false , "targets": [1,2] }]
        });
    } );

    $("#add_sy").unbind().click(function(){
        manageSchoolYear('');
    });

    $(".edit_sy").unbind().click(function(){
        manageSchoolYear($(this), $(this).attr("tbl_id"));
    })

    $("#sytable").delegate(".delete_sy", "click", function(){
        var tbl_id = $(this).attr("tbl_id");
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
                url: "<?=site_url('reportsitem_/deleteSchoolYear')?>",
                type: "POST",
                data : {id:tbl_id},
                success: function(res){
                  Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'School year has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1500
                    })
                  loadSchoolYearData();
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
    })

    function manageSchoolYear(edit = '', id=''){
        $("#modal-view").find("h3[tag='title']").text(edit != '' ? 'Edit School Year': 'Add School Year');
        $("#button_save_modal").text("Save");  
        $.ajax({
            url: "<?=site_url('reportsitem_/manageSchoolYear')?>",
            type: "POST",
            data : {id : id},
            success: function(msg){
               $("#modal-view").find("div[tag='display']").html(msg);
            }
        }); 
    }
</script>