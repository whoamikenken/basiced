<?php
	  $sctt = $this->db->query("SELECT a.id as tbl_id,a.employeeid,a.subj_id,b.subj_code,b.description,a.remarks, a.status, a.dra_remarks FROM employee_subj_competent_to_teach a LEFT JOIN code_subj_competent_to_teach b ON a.subj_id=b.id WHERE a.employeeid='$employeeid'")->result();

	if(count($sctt)>0){
	    foreach($sctt as $row){
	      $sctt = Globals::result_XHEP($sctt);
	      $filename = $content = $mime = '';
	      list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_subj_competent_to_teach", $row->tbl_id);
	?>
	
	    <tr id="<?= $row->tbl_id ?>" table="employee_subj_competent_to_teach" style="border-top: 1px solid #ddd !important;">
	        <td relsctt='<?=$row->subj_id?>'><?=$row->subj_code?></td>
	        <!-- <td><?=$row->description?></td> -->
	        <td><?=$row->remarks?></td>
	        <td>
	            <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $row->tbl_id ?>"  type="file" style="visibility:hidden" required="required"/>
	        </td>
	        <td  class="tooltip" id="<?= $row->tbl_id ?>" table="employee_subj_competent_to_teach">
	            <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $row->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$row->status?><span class="tooltiptext tooltiptext_<?=$row->tbl_id?>_employee_subj_competent_to_teach">Loading..</span></a><?php } ?>
	            <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $row->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> "> <?=$row->status?></a><?php } ?>
	        </td>
	         <td><?=$row->dra_remarks?></td>
	        <td>
	          <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $row->status != 'APPROVED')){ ?>
	              <div style="float: right;">
	                <a class='btn btn-primary edit_sctt' href='#modal-view' data-toggle='modal' style="margin-right: 10px;" tbl_id = "<?=$row->tbl_id?>"><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_sctt' tbl_id = "<?=$row->tbl_id?>"><i class='glyphicon glyphicon-trash'></i></a>
	            </div>
	          <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $row->status=='APPROVED'){ ?>
	            <div style="float: right;">
	                <a class='btn btn-primary edit_sctt' href='#modal-view' data-toggle='modal' style="margin-right: 10px;" tbl_id = "<?=$row->tbl_id?>"><i class='glyphicon glyphicon-edit'></i></a>
	            </div>
	          <?php } ?>
	        </td>
	    </tr>    
	<?                            
	    }
	}
?>                                        
<script type="text/javascript">
	$("#scttlist").dataTable();

	$('#scttlist tbody').on('click', '.edit_sctt', function () {
	    addSctt($(this), $(this).attr("tbl_id"));
	});

	$('#scttlist tbody').on('click', '.delete_sctt', function () {
	    var mtable = $("#scttlist").find("tbody");
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
	        deleteSctt($(this), $(this).attr("tbl_id"));
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

	function deleteSctt(obj, tbl_id = ""){
	    var table = "";
	    var userid = "";
	    if($("input[name='applicantId']").val()){
	        table = "applicant_subj_competent_to_teach";
	        userid = $("input[name='applicantId']").val();
	    }
	    else{
	        table = "employee_subj_competent_to_teach"; 
	        userid = $("input[name='employeeid']").val();
	    }
	    $.ajax({
	        url: $("#site_url").val() + "/employee_/deleteData",
	        type: "POST",
	        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
	        dataType: "JSON",
	        success: function(msg){ 
	        	loadTable('employee_subj_competent_to_teach_table');
	            Swal.fire({
	                icon: 'success',
	                title: 'Success!',
	                text: 'Successfully deleted!',
	                showConfirmButton: true,
	                timer: 1000
	            })
	        }
	    });  
	}

	$('#scttlist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#scttlist .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   