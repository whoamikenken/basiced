<?php
	 $pgd = $this->db->query("SELECT publication,title,publisher,datef,type, e.id as id, r.level as level, e.status, e.dra_remarks from employee_pgd e LEFT JOIN reports_item r ON e.publication = r.ID where employeeid='{$employeeid}' ORDER BY datef DESC")->result();

	if(count($pgd)>0){
    $pgd = Globals::result_XHEP($pgd);
    foreach($pgd as $sm){
      $filename = $content = $mime = '';
    list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_pgd", $sm->id, $employeeid);
?>
    <tr id="<?= $sm->id ?>" table="employee_pgd"  style="border-top: 1px solid #ddd !important;">
        <!-- <td educpgd='<?=$sm->ID?>'><?=$sm->publication?></td> -->
         <td relpublication="<?=$sm->publication?>"><?=$sm->publication?></td>
        <td><?=$sm->title?></td>
        <td><?=$sm->publisher?></td>
        <td><?=$sm->datef?></td>
        <td><?=$sm->type?></td>
        <td>
            <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $sm->id ?>"  type="file" style="visibility:hidden; width: 20px" required="required"/>
        </td>
        <td class="tooltip" id="<?= $sm->id ?>" table="employee_pgd">
            <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$sm->status?><span class="tooltiptext tooltiptext_<?=$sm->id?>_employee_pgd">Loading..</span></a><?php } ?>
            <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> "> <?=$sm->status?></a><?php } ?>
        </td>
        <td><?=$sm->dra_remarks ?></td>
        <td class="align_center">
         <!--  <?php if ($this->session->userdata("usertype") == "ADMIN"): ?>
            <div id="btn_pos">
            <a class='btn btn-primary edit_pgd' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_pgd' tbl_id="<?=$sm->id?>" ><i class='glyphicon glyphicon-trash'></i></a>
            </div>
          <?php endif ?> -->
          <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status != 'APPROVED')){ ?>
              <div id="btn_pos">
              <a class='btn btn-primary edit_pgd' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_pgd' tbl_id="<?=$sm->id?>" ><i class='glyphicon glyphicon-trash'></i></a>
            </div>
          <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status=='APPROVED'){ ?>
            <div id="btn_pos">
              <a class='btn btn-primary edit_pgd' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
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
  $("#pgdinfolist").dataTable();

    $('#pgdinfolist tbody').on('click', '.edit_pgd', function () {
        addPGD($(this), $(this).attr("tbl_id"));
    });

    $('#pgdinfolist tbody').on('click', '.delete_pgd', function () {
    var mtable = $("#pgdinfolist").find("tbody");
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
      
    deletePGD($(this), $(this).attr("tbl_id"));
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


  $('#pgdinfolist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#pgdinfolist  .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   