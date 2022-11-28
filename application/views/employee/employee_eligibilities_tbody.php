<?php
	 $eligibilities = $this->db->query("SELECT * from employee_eligibilities where employeeid='$employeeid'")->result();

	if(count($eligibilities)>0){
        foreach($eligibilities as $el){
          $eligibilities = Globals::result_XHEP($eligibilities, $employeeid);
          $filename = $content = $mime = '';
          list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_eligibilities", $el->id);
          if($content == '' && $mime = ''){
            $content = $el->content;
            $mime = $el->mime;
            $filename = $el->filename;
          }
?>
        <tr id="<?= $el->id ?>" table="employee_eligibilities" style="border-top: 1px solid #ddd !important;">
            <td desc='<?=$el->description?>'><?=$el->description?></td>
            <td><?=$el->license_number?></td>
            <td><?=$el->date_issued?></td>
            <td><?=$el->date_expired?></td>
            <td><?=$el->remarks?></td>
            <td>
                <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $el->id ?>"  type="file" style="visibility:hidden" required="required"/>
            </td>
           <td class="tooltip" id="<?= $el->id ?>" table="employee_eligibilities" style="border-top: 0px solid #ddd !important;">
                <?php if($this->session->userdata("usertype") == "ADMIN"){ ?>
                  <a class="btn <?= $el->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status "> <?=$el->status?>
                    <span class="tooltiptext tooltiptext_<?=$el->id?>_employee_eligibilities">Loading..</span>
                  </a><?php } ?>
                <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?>
                  <a class="btn <?= $el->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> "> <?=$el->status?>
                  </a><?php } ?>
            </td>
             <td><?=$el->dra_remarks?></td>
            <td>
              
              <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $el->status != 'APPROVED')){ ?>
                  <div style="float: right;">
                    <a class='btn btn-primary edit_eligibility' href='#modal-view' data-toggle='modal' style="margin-right: 10px;" tbl_id = "<?=$el->id?>"><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_eligibility' tbl_id = "<?=$el->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                </div>
              <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $el->status=='APPROVED'){ ?>
                <div style="float: right;">
                    <a class='btn btn-primary edit_eligibility' href='#modal-view' data-toggle='modal' style="margin-right: 10px;" tbl_id = "<?=$el->id?>"><i class='glyphicon glyphicon-edit'></i></a>
                </div>
              <?php } ?>
            </td>
        </tr>    
<?php                            
        }
   }
?>                     
<script type="text/javascript">
	$("#eligibilitieslist").dataTable();
	$('#eligibilitieslist tbody').on('click', '.edit_eligibility', function () {
	    addeligibilities($(this), $(this).attr("tbl_id"));
	});

	$('#eligibilitieslist tbody').on('click', '.delete_eligibility', function () {
	    var mtable = $("#eligibilitieslist").find("tbody");
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
	        
	        deleteEligibility($(this), $(this).attr("tbl_id"));
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

	function deleteEligibility(obj, tbl_id = ""){
	    var table = "";
	    var userid = "";
	    if($("input[name='applicantId']").val()){
	        table = "applicant_eligibilities";
	        userid = $("input[name='applicantId']").val();
	    }
	    else{
	        table = "employee_eligibilities"; 
	        userid = $("input[name='employeeid']").val();
	    }
	    $.ajax({
	        url: $("#site_url").val() + "/employee_/deleteData",
	        type: "POST",
	        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
	        dataType: "JSON",
	        success: function(msg){ 
	        	loadTable('employee_eligibilities_table');
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

	$('#eligibilitieslist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#eligibilitieslist .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   