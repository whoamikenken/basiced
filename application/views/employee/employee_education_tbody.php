<?php
	$educational_background = $this->db->query("SELECT DISTINCT completed,school,course,units,year_graduated,date_graduated,datefrom,dateto,e.educ_level,r.level , e.id, e.status, a.description as schoolDesc, a.schoolid, e.dra_remarks from employee_education e INNER JOIN reports_item r ON e.educ_level = r.level INNER JOIN code_school a ON e.schoolid = a.schoolid  where employeeid='$employeeid'")->result();

	if(count($educational_background)>0){
	    foreach($educational_background as $eb){
	    $filename = $content = $mime = '';
	    list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_education", $eb->id);
	    $eb->schoolDesc = str_replace("'S", "'s", $eb->schoolDesc);
	    foreach ($eb as $key => $value) $eb->$key = Globals::_e($value);
	?>
	    <tr id="<?= $eb->id ?>" table="employee_education" style="border-top: 1px solid #ddd !important;">
	        <td schoolid='<?= $eb->schoolid ?>'><?=$eb->schoolDesc ?></td>
	        <td reldata='<?=$eb->educ_level?>'><?=$eb->educ_level?></td>
	        <td><?=$eb->course?></td>
	        <td><?=($eb->units != 0 ? $eb->units : '' )?></td>
	        <td><?=$eb->date_graduated?></td>
	        <td  completed="<?= $eb->completed ?>">
	            <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $eb->id ?>"  type="file" style="visibility:hidden" required="required"/>
	        </td>
	        
	        <td class="tooltip" id="<?= $eb->id ?>" table="employee_education">
	            <?php if($this->session->userdata("usertype") == "ADMIN"){ ?>
	              <a class="btn <?= $eb->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status "> <?=$eb->status?>
	                <span class="tooltiptext tooltiptext_<?=$eb->id?>_employee_education">Loading..</span>
	              </a><?php } ?>
	            <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?>
	              <a class="btn <?= $eb->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> "> <?=$eb->status?>
	                
	              </a><?php } ?>
	        </td>
	        <td><?=$eb->dra_remarks?></td>
	        <td>
	          <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $eb->status != 'APPROVED')){ ?>
	              <div style="float: right;">
	                <a class='btn btn-primary edit_educlevel' href='#modal-view' data-toggle='modal' tbl_id = "<?=$eb->id?>" style="margin-right: 10px;"><i class='glyphicon glyphicon-edit'></i></a>
	                <a class='btn btn-warning delete_educlevel' tbl_id = "<?=$eb->id?>"><i class='glyphicon glyphicon-trash'></i></a>
	            </div>
	          <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $eb->status=='APPROVED'){ ?>
	            <div style="float: right;">
	                <a class='btn btn-primary edit_educlevel' href='#modal-view' data-toggle='modal' tbl_id = "<?=$eb->id?>" style="margin-right: 10px;"><i class='glyphicon glyphicon-edit'></i></a>
	            </div>
	          <?php } ?>
	        </td>
	    </tr>
	<?php                            
	    }
	}
	?>    
<script type="text/javascript">
	$("#educationlist").dataTable();

	$('#educationlist tbody').on('click', '.edit_educlevel', function () {
	    addeducation($(this), $(this).attr("tbl_id"));
	});

	$('#educationlist tbody').on('click', '.delete_educlevel', function () {
	    var mtable = $("#educationlist").find("tbody");
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
	        deleteEduclevel($(this), $(this).attr("tbl_id"));
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

	function deleteEduclevel(obj, tbl_id = ""){
	    var table = "";
	    var userid = "";
	    if($("input[name='applicantId']").val()){
	        table = "applicant_education";
	        userid = $("input[name='applicantId']").val();
	    }
	    else{
	        table = "employee_education"; 
	        userid = $("input[name='employeeid']").val();
	    }
	    $.ajax({
	        url: $("#site_url").val() + "/employee_/deleteData",
	        type: "POST",
	        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
	        dataType: "JSON",
	        success: function(msg){ 
	        	loadTable('employee_education_table');
	            Swal.fire({
	                icon: 'success',
	                title: 'Success!',
	                text: 'Successfully deleted data!',
	                showConfirmButton: true,
	                timer: 1000
	            })
	        }
	    });  
	}

	$('#educationlist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#educationlist .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   