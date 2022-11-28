<?php
	$org   = $this->db->query("SELECT * from employee_proorg  where employeeid='{$employeeid}'")->result();

	 if(count($org)>0){
    $org = Globals::result_XHEP($org);
    foreach($org as $sm){
      $filename = $content = $mime = '';
list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_proorg", $sm->id, $employeeid);
?>
    <tr id="<?= $sm->id ?>" table="employee_proorg"  style="border-top: 1px solid #ddd !important;">
        <td><?=$sm->name_org?></td>
        <!-- <td educmapo='<?=$sm->ID?>'><?=$sm->level?></td> -->
        <td><?=$sm->datef?></td>
        <td><?=$sm->position?></td>
        <td>
            <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $sm->id ?>"  type="file" style="visibility:hidden; width: 20px" required="required"/>
        </td>
        <td class="tooltip" id="<?= $sm->id ?>" table="employee_proorg">
            <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$sm->status?><span class="tooltiptext tooltiptext_<?=$sm->id?>_employee_proorg">Loading..</span></a><?php } ?>
            <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?>"> <?=$sm->status?></a><?php } ?>
        </td>
        <td><?=$sm->dra_remarks?></td>
        <td class="align_center">

          <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status != 'APPROVED')){ ?>
              <div id="btn_pos">
              <a class='btn btn-primary edit_org' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_org' tbl_id="<?=$sm->id?>"><i class='glyphicon glyphicon-trash'></i></a>
            </div>
          <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status=='APPROVED'){ ?>
            <div id="btn_pos">
              <a class='btn btn-primary edit_org' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
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
  $("#orginfolist").dataTable();

     $('#orginfolist tbody').on('click', '.edit_org', function () {
        addOrg($(this), $(this).attr("tbl_id"));
    });

     $('#orginfolist tbody').on('click', '.delete_org', function () {
    var mtable = $("#orginfolist").find("tbody");
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
        //  if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
        // $(this).parent().parent().parent().remove();
        deleteOrg($(this), $(this).attr("tbl_id"));
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

  $('#orginfolist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#orginfolist  .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   