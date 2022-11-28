<table class="table table-bordered table-hover datatable" style="width: 100%;"  id="uploaded_tables">
    <thead>
        <tr style="background-color: #0072c6;font-weight: bold; color:black;">
            <th class="align_center" style="font-weight: bold;">Description</th>
            <th class="align_center" style="font-weight: bold;">Filename</th>
            <th class="align_center" style="font-weight: bold;">Uploaded By</th>
            <th class="align_center" style="font-weight: bold;">Date</th>
            <th class="align_center" style="font-weight: bold;">Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php if($records) foreach($records as $value):?>
			<tr id="<?= $value->id ?>">
				<td class="align_center exist-desc exist-desc_<?= $value->id?>"><?= $value->description?></td>
				<td class="align_center exist-id exist-id_<?= $value->id?>"><?= $value->filename?></td>
				<td class="align_center"><?= $value->uploaded_by?></td>
				<td class="align_center"><?= date("Y-m-d", strtotime($value->date_upload));?></td>
				<td class="align_center">
					<b><a class="btn btn-info view_image view_image_<?= $value->id?>" id="<?= $value->id?>"><i class="glyphicon glyphicon-eye-open"></i></a><b>
					<b><a class="btn btn-primary edit_forms edit_forms_<?= $value->id?>" id="<?= $value->id?>" desc="<?= $value->description ?>" filename="<?= $value->filename?>"><i class="glyphicon glyphicon-edit"></i></a></b>
					<b><a class="btn btn-danger delete_forms delete_forms_<?= $value->id?>" id="<?= $value->id?>"><i class="glyphicon glyphicon-trash"></i></a></b>
				</td>
			</tr>
		<?php endforeach ?>
    </tbody>
</table>
<div class="modal fade" id="view_image_modal" role="dialog"></div>
<script type="text/javascript">
	$("#uploaded_tables").dataTable({
	    "sPaginationType": "full_numbers",
	    "oLanguage": {
	                     "sEmptyTable":     "No Data Available.."
	                 },
	    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
	});

	$('#uploaded_tables tbody').on('click', '.view_image', function () {
		var id = $(this).attr("id");
		$.ajax({
            url: $("#site_url").val() + '/documents_/viewImageModal',
            data: {id:id},
            type: "POST",
            success:function(response){
                $("#view_image_modal").modal('toggle');
                $("#view_image_modal").html(response);
            }
        });
	})

	$("table").delegate('.delete_forms', 'click', function(){
        var id = $(this).attr('id');
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: "Are you sure?",
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
            deleteDocumentRecord(id)
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

     function deleteDocumentRecord(id){
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + '/documents_/deleteUploadedForm',
            data: {id:id},
            success:function(response){
                Swal.fire({
                  	icon: 'success',
                  	title: 'Success!',
                  	text: "Form has been deleted successfully.",
                  	showConfirmButton: true,
                  	timer: 1000
              	})
                uploadedTable();
            }
        });
    }

    $(".edit_forms").click(function(){
    	$("#upload_btn").attr("tag", "edit").text("Update");
    	var desc = $(this).attr("desc");
      var id = $(this).attr("id");
    	var filename = $(this).attr("filename");
    	$("input[name='id']").val(id);
      $("input[name='msg']").val("Form has been updated successfully.");
    	$("input[name='description']").val(desc);
      var baseurl = "<?=base_url()."application/uploads/"?>";
      $("#uploaded_link").attr("href", baseurl+filename);
      $("#uploaded_link").show();
    	$("#cancel_btn").css("display", "unset");
      
    })
</script>