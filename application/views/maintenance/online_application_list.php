<?php 
	if(isset($app_list) && $app_list){
		foreach($app_list as $mrow){ ?>
			<tr>	
				<td tag='deduct' class="align_center col-md-1">
                	<div class="btn-group">
                		<a class="btn btn-danger" tag="delete_d" code='<?php echo $mrow->id?>'><i class="glyphicon glyphicon-trash"></i></a> 
                    	<button type="button" class="btn btn-info" data-toggle="collapse" data-target="#content-<?php echo  $mrow->id ?>"><span class="glyphicon glyphicon-arrow-down"></span></button>
                    </div>
                </td>

                <td>
                    <b><?php echo  $mrow->description ?></b>
                    <div id="content-<?php echo  $mrow->id ?>" class="collapse">
						<table class="table table-hover table-striped table-bordered app_base_list">
							<thead>	
								<tr style="color: black;">	
									<th>Actions</th>
									<th>Code</th>
									<th>Description</th>
									<th>Applicable Gender</th>
									<th>Approval Sequence</th>
								</tr>
							</thead>
							<tbody>
								<?php 
									$gender = "";
									if($app_base_list[$mrow->id]){
										foreach($app_base_list[$mrow->id] as $row){ 
											if($row->ismain) $gender = $row->genderApplicable;
											?>
											<tr>
												<td class="	align_center"><a class="btn btn-info" href="#modal-view" tag='edit_d' ismain="<?php echo  $row->ismain ?>" data-toggle="modal" code='<?php echo $row->code_request?>'><i class="glyphicon glyphicon-edit"></i></a></td>
												<td class="	align_center"><?php echo  strtoupper($row->code_request) ?></td>
												<td class="	align_center"><?php echo  strtoupper($row->description) ?></td>
												<td class="	align_center"><?php echo  strtoupper($gender) ?></td>
												<td class="	align_center"><a href="#" tag='view_app' data-toggle="modal" data-target="#modal-view" code='<?php echo $row->code_request?>' title="View Approval Sequence" ><i class="icon-large icon-eye-open"></i></a></td>
											</tr>
									<?php
										}
									} 
								?>
							</tbody>
						</table>                       
                    </div>
                </td>
			</tr>

<?php
		}
	} 
?>

<script>

	$(document).ready(function(){
	    var table = $('.app_base_list').dataTable({
		    "bPaginate": false,
		    "bLengthChange": false,
		    "bFilter": false,
		    "bAutoWidth": false 
		});

		$(document).ready(function(){
		    var table = $('#leave_request').DataTable({
		    });
		    new $.fn.dataTable.FixedHeader( table );
		});
	});

	$( "table" ).delegate( "#addleave,a[tag='edit_d']", "click", function() {
	    var code = "";  
	    if($(this).attr("code")) code = $(this).attr("code");
	    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Leave Type Setup" : "Add Leave Type Setup");
	    $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
	    $("#button_save_modal").text("Save");     
	    var form_data = {
	        code:  GibberishAES.enc(code , toks),
	        toks:toks
	    };

	    var ismain = $(this).attr("ismain");

	    if(ismain==1 || !code){
		    $.ajax({
		        url: "<?php echo site_url('maintenance_/manage_leave')?>",
		        type: "POST",
		        data: form_data,
		        success: function(msg){
		            $("#modal-view").find("div[tag='display']").html(msg);
		            $("#modal-view").find("#button_save_modal").show();

		        }
		    });  
		}else{
			$.ajax({
		        url: "<?php echo site_url('maintenance_/manage_other_leave')?>",
		        type: "POST",
		        data: form_data,
		        success: function(msg){
		            $("#modal-view").find("div[tag='display']").html(msg);
		            $("#modal-view").find("#button_save_modal").show();
		        }
		    });  
		}

	});

	$("#addleave").click(function(){
	    var code = "";  
	    if($(this).attr("code")) code = $(this).attr("code");
	    $("#modal-view").find("h3[tag='title']").text(code ? "Edit Leave Type Setup" : "Add Leave Type Setup");
	    $("#modal-view").find(".modal-dialog").removeClass("modal-md").addClass("modal-lg");
	    $("#button_save_modal").text("Save");     
	    var form_data = {
	        code:  GibberishAES.enc(code , toks),
	        toks:toks
	    };

	    var ismain = $(this).attr("ismain");

	    if(ismain==1 || !code){
		    $.ajax({
		        url: "<?php echo site_url('maintenance_/manage_leave')?>",
		        type: "POST",
		        data: form_data,
		        success: function(msg){
		            $("#modal-view").find("div[tag='display']").html(msg);
		            $("#modal-view").find("#button_save_modal").show();

		        }
		    });  
		}else{
			$.ajax({
		        url: "<?php echo site_url('maintenance_/manage_other_leave')?>",
		        type: "POST",
		        data: form_data,
		        success: function(msg){
		            $("#modal-view").find("div[tag='display']").html(msg);
		            $("#modal-view").find("#button_save_modal").show();
		        }
		    });  
		}

	});

	$("#leave_request").delegate("a[tag='delete_d']", "click", function(){
		 const swalWithBootstrapButtons = Swal.mixin({
			customClass: {
			confirmButton: 'btn btn-success',
			cancelButton: 'btn btn-danger'
			},
			buttonsStyling: false
		})

		swalWithBootstrapButtons.fire({
			title: 'Are you sure?',
			text: "Do you really want to delete this code?",
			icon: 'warning',
			showCancelButton: true,
			confirmButtonText: 'Yes, proceed!',
			cancelButtonText: 'No, cancel!',
			reverseButtons: true
		}).then((result) => {
		if (result.value) {
			var form_data = {
		        code:  GibberishAES.enc($(this).attr("code") , toks),
		        toks:toks
		    };
			$.ajax({
				url:"<?php echo site_url("maintenance_/deleteOnlineApplicationCode")?>",
				type:"POST",
				data:form_data,
				dataType: "json",
				success: function(response){
					if(response.err == 0){
						Swal.fire({
			                icon: 'success',
			                title: 'Success!',
			                text: response.msg,
			                showConfirmButton: true,
			                timer: 2000
			            });
						$(".nav-tabs > li.active > a").click();
					}else{
						Swal.fire({
			                icon: 'warning',
			                title: 'Warning!',
			                text: response.msg,
			                showConfirmButton: true,
			                timer: 2000
			            });
					}
				}
			}); 
		} else if (
			result.dismiss === Swal.DismissReason.cancel
		) {
				swalWithBootstrapButtons.fire(
				'Cancelled',
				'Application is safe.',
				'error'
				)
			}
		});
	});

	$("a[tag='view_app']").click(function(){
	    var code = "";  
	    if($(this).attr("code")) code = $(this).attr("code");
	    $("#modal-view").find("h3[tag='title']").text("View Approval Sequence");
	    var form_data = {
	        code:  GibberishAES.enc(code , toks),
	        toks:toks
	    };

	    $.ajax({
	        url: "<?php echo site_url('maintenance_/onlineApplicationApproverSeq')?>",
	        type: "POST",
	        data: form_data,
	        success: function(msg){
	            $("#modal-view").find("div[tag='display']").html(msg);
	            $("#modal-view").find("#button_save_modal").hide();

	        }
	    });  

	});

</script>