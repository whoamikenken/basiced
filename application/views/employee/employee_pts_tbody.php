<?php
	 $pts = $this->db->query("SELECT b.level AS title, b.level AS venue, a.id as id, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.location, a.dra_remarks, a.other_title FROM employee_pts a LEFT JOIN reports_item b ON b.level = a.venue WHERE employeeid='{$employeeid}' ORDER BY a.datef DESC")->result();

	if(count($pts)>0){
        $pts = Globals::result_XHEP($pts);
        foreach($pts as $sm){
          $filename = $content = $mime = '';
          list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_pts", $sm->id, $employeeid);
          // echo "<pre>"; print_r($this->db->last_query());
?>

        <tr id="<?= $sm->id ?>" table="employee_pts" style="border-top: 1px solid #ddd !important;">
            <td reltitle="<?=$sm->title_id?>" other_title="<?=$sm->other_title?>"><?=($sm->title_id == 'others' ? $sm->other_title : $sm->title_id)?></td>
            <td><?=$sm->datef?></td>
            <td><?=$sm->organizer?></td>
            <!-- <td relvenue="<?=$sm->venue_id?>"><?=$sm->venue?></td> -->
            <td><?= $sm->location ?></td>
            <td>
              <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $sm->id ?>"  type="file" style="visibility:hidden; width: 20px" required="required"/>
            </td>
            <td class="tooltip" id="<?= $sm->id ?>" table="employee_pts">
                <?php if($this->session->userdata("usertype") == "ADMIN"){ ?>
                <a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$sm->status?>
                    <span class="tooltiptext tooltiptext_<?=$sm->id?>_employee_pts">Loading..</span>
                </a><?php } ?>
                <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> "> <?=$sm->status?>
                </a><?php } ?>
            </td>
            <td><?= $sm->dra_remarks ?></td>
            <td class="align_center">
<!--                              <?php if ($this->session->userdata("usertype") == "ADMIN"): ?>
                <div id="btn_pos">
                  <a class='btn btn-primary edit_pts' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_pts' tbl_id="<?=$sm->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                </div>
              <?php endif ?> -->
              <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status != 'APPROVED')){ ?>
                  <div id="btn_pos">
                  <a class='btn btn-primary edit_pts' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_pts' tbl_id="<?=$sm->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                </div>
              <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status=='APPROVED'){ ?>
                <div id="btn_pos">
                  <a class='btn btn-primary edit_pts' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
                </div>
              <?php } ?>
            </td>
        </tr>    
<?                            
        }
   }else{
?>
        <!-- <tr>
            <td colspan="6">No existing data</td>
        </tr> -->
<?                    
   }
?>                                         
<script type="text/javascript">
	$("#ptsinfolist").dataTable();

	$('#ptsinfolist tbody').on('click', '.edit_pts', function () {
        addpts($(this), $(this).attr("tbl_id"));
    });


	$('#ptsinfolist tbody').on('click', '.delete_pts', function () {
	    var mtable = $("#ptsinfolist").find("tbody");
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
	        
	        deletePTS($(this), $(this).attr("tbl_id"));
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

	$('#ptsinfolist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#ptsinfolist .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});

	
</script>                   