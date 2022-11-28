<table class="table table-hover table-bordered" id="set_table">
    <thead style="background-color: #0072c6">
        <tr>
            <th>ID #</th>
            <th>Description</th>
            <th></th>
        </tr>
    </thead>
    
    <tbody id="data_body_type">
    	<?php if(isset($records)){ ?>  
		    <?php foreach($records as $value): ?>
		        <tr>
		            <td> <?= $value['id']?> </td>
		            <td> <?= $value['description']?> </td>
		            <td> <a class="btn btn-info edit_dataset" categ="<?= $categ ?>" id="<?=$value['id']?>" ><i class="glyphicon glyphicon-edit"></i></a>
		            <a class="btn btn-danger delete_dataset" desc="<?= $value['description']?>" categ="<?= $categ ?>" id="<?=$value['id']?>" ><i class="glyphicon glyphicon-trash"></i></a> </td>
		        </tr>
		    <?php endforeach ?>
		<?php } ?>
    </tbody>
</table>

<script type="text/javascript">
	$("#set_table").dataTable({
        "pagination": "number",
        "oLanguage": {
                         "sEmptyTable":     "No Data Available.."
                     },
        "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]],
        "pageLength": 10,
        "scrollY": false,
        "scrollX": false
    });

	$("tbody").delegate(".edit_dataset", "click", function(){
		$("#description").val('');
		categ = $(this).attr('categ');
		$(".modal-title").text('Edit ' + categ.charAt(0).toUpperCase() + categ.slice(1) +  ' Config');
		var id = $(this).attr('id');
		$.ajax({
		    url: "<?= site_url('setup_/editPayrollRankSetup') ?>",
		    type: "POST",
		    dataType: "JSON",
		    data: {id: id, categ: categ},
		    success:function(res){
		        $("#add_setup").modal('toggle');
		        $("#code").val(res.id);
		        $("#description").val(res.description);
		    }
		});
	});
	$("tbody").delegate(".delete_dataset", "click", function(){
	    
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
	    categ = $(this).attr('categ').toUpperCase();
	    desc = $(this).attr('desc').toUpperCase();
				$.ajax({
					url: "<?= site_url('setup_/deletePayrollRankSetup') ?>",
					type: "POST",
					data: {id:id, categ:categ,desc:desc},
					success:function(response){
                // alert(response);
                // location.reload();
                Swal.fire({
                	icon: 'success',
                	title: 'Success!',
                	text: "Set has been deleted successfully",
                	showConfirmButton: true,
                	timer: 1000
                })
                // loadtypeconfig();          
                // loadrankconfig();       
                loadsetconfig();
                // $("#modalclose").click();
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

	// $("tbody").delegate(".delete_dataset", "click", function(){
	//     id = $(this).attr('id');
	//     categ = $(this).attr('categ').toUpperCase();
	//     desc = $(this).attr('desc').toUpperCase();
	//     $(".modal-title").text('DELETE ' + categ + ' CONFIG');
	//     $("#deleteconfig").attr("deleteID", id);
	//     $("#deleteconfig").attr("deletecategory", categ);
	//     $("#managerank_id").html("<b>" + desc + "</b> from  <b>" + categ +" CONFIG?</b>");
	//     $("#deletemodal").modal('toggle');
 //    });

</script>