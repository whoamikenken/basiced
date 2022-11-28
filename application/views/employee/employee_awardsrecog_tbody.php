<?php
	 $ar = $this->db->query("SELECT award,institution,datef,r.level,r.description,r.ID,address, e.id as tbl_id, e.status, e.dra_remarks FROM employee_awardsrecog e LEFT JOIN reports_item r ON e.award = r.ID WHERE employeeid='{$employeeid}' ORDER BY datef DESC")->result();

	if(count($ar)>0){
      $ar = Globals::result_XHEP($ar);
      foreach($ar as $sm){
        $filename = $content = $mime = '';
      list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_awardsrecog", $sm->tbl_id, $employeeid);
?>
      <tr id="<?= $sm->tbl_id ?>" table="employee_awardsrecog" style="border-top: 1px solid #ddd !important;">
          <td relaward='<?=$sm->award?>'><?=$sm->award?></td>
          <td><?=$sm->institution?></td>
          <td><?=$sm->address?></td>
          <td><?=$sm->datef?></td>
          <td>
              <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $sm->tbl_id ?>"  type="file" style="visibility:hidden; width: 20px" required="required"/>
          </td>
          <td class="tooltip" id="<?= $sm->tbl_id ?>" table="employee_awardsrecog">
              <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$sm->status?><span class="tooltiptext tooltiptext_<?=$sm->tbl_id?>_employee_awardsrecog">Loading..</span></a><?php } ?>
              <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?>"> <?=$sm->status?></a><?php } ?>
          </td>
          <td><?=$sm->dra_remarks?></td>
          <td class="align_center">
            <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status != 'APPROVED')){ ?>
                <div id="btn_pos">
                <a class='btn btn-primary edit_ar' tbl_id="<?=$sm->tbl_id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_ar' tbl_id="<?=$sm->tbl_id?>"><i class='glyphicon glyphicon-trash'></i></a>
              </div>
            <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status=='APPROVED'){ ?>
              <div id="btn_pos">
                <a class='btn btn-primary edit_ar' tbl_id="<?=$sm->tbl_id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
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
   $("#arinfolist").dataTable();
    $('#arinfolist tbody').on('click', '.edit_ar', function () {
        addAward($(this), $(this).attr("tbl_id"));
    });

    $('#arinfolist tbody').on('click', '.delete_ar', function () {
    var mtable = $("#arinfolist").find("tbody");
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
    //     if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
    // $(this).parent().parent().parent().remove();
    deleteAWARD($(this), $(this).attr("tbl_id"));
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


  $('#arinfolist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#arinfolist .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   