<?php
	$work_history_related = $this->db->query("SELECT * from employee_work_history_related where employeeid='$employeeid'")->result();

	 if(count($work_history_related)>0){
        $work_history_related = Globals::result_XHEP($work_history_related);
        foreach($work_history_related as $wh){
          $filename = $content = $mime = '';
          list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_work_history_related", $wh->id);
?>

        <tr id="<?= $wh->id ?>" table="employee_work_history_related"  style="border-top: 1px solid #ddd !important;">
            <td><?=$wh->position?></td>
            <td><?=$wh->company?></td>
            <!-- <td><?=$wh->address?></td> -->
            <!-- <td><?=$wh->contactnumber?></td> -->
            <td><?= $wh->remarks ?></td>
            <!-- <td><?= number_format($wh->salary,2)?></td> -->
            <td><?=$wh->reason?></td>
            <td>
                <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $wh->id ?>"  type="file" style="visibility:hidden" required="required"/>
            </td>
            <td class="tooltip" id="<?= $wh->id ?>" table="employee_work_history_related" >
                <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $wh->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$wh->status?><span class="tooltiptext tooltiptext_<?=$wh->id?>_employee_work_history_related">Loading..</span></a><?php } ?>
                <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $wh->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> "> <?=$wh->status?></a><?php } ?>
            </td>
            <td><?=$wh->dra_remarks?></td>
            <!--<td class="align_center"><?=$wh->date_from?></td>
            <td class="align_center"><?=$wh->date_to?></td>-->
            <td>
              <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $wh->status != 'APPROVED')){ ?>
                  <div style="float: right">
                    <a class='btn btn-primary edit_whr' href='#modal-view' data-toggle='modal' tbl_id="<?=$wh->id?>" style="margin-right: 10px;"><i class='glyphicon glyphicon-edit'></i></a>&nbsp;<a class='btn btn-warning delete_whr' tbl_id="<?=$wh->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                </div>
              <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $wh->status=='APPROVED'){ ?>
                <div style="float: right">
                    <a class='btn btn-primary edit_whr' href='#modal-view' data-toggle='modal' tbl_id="<?=$wh->id?>" style="margin-right: 10px;"><i class='glyphicon glyphicon-edit'></i></a>
                </div>
              <?php } ?>
            </td>
        </tr>    
<?                            
        }
   }
?>                                                      
<script type="text/javascript">
	$("#workhistorylistrelated").dataTable();
	$('#workhistorylistrelated tbody').on('click', '.edit_whr', function () {
	    addworkhistoryrelated($(this), $(this).attr("tbl_id"));
	});

	$('#workhistorylistrelated tbody').on('click', '.delete_whr', function () {
	    var mtable = $("#workhistorylistrelated").find("tbody");
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
	        deleteWHR($(this), $(this).attr("tbl_id"));
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

	function deleteWHR(obj, tbl_id = ""){
	    var table = "";
	    var userid = "";
	    if($("input[name='applicantId']").val()){
	        table = "applicant_work_history_related";
	        userid = $("input[name='applicantId']").val();
	    }
	    else{
	        table = "employee_work_history_related"; 
	        userid = $("input[name='employeeid']").val();
	    }
	    $.ajax({
	        url: $("#site_url").val() + "/employee_/deleteData",
	        type: "POST",
	        data: {table: GibberishAES.enc(table , toks), tbl_id: GibberishAES.enc( tbl_id, toks), employeeid:  GibberishAES.enc(userid , toks), toks:toks},
	        dataType: "JSON",
	        success: function(msg){ 
	        loadTable('employee_work_history_related_table');
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

	$('#workhistorylistrelated tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#workhistorylistrelated .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   