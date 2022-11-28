<?php
	 $scho = $this->db->query("SELECT s.id as id, s.type_of_scho, s.gr_agency, s.prog_study, s.ins_scho, s.datef, s.dateto, r.ID, r.level as scholarship, s.status, s.dra_remarks from employee_scholarship s LEFT JOIN reports_item r ON s.type_of_scho = r.ID where employeeid='{$employeeid}' ORDER BY s.datef DESC")->result();
   // echo "<pre>"; print_r($this->db->last_query()); die;
	if(count($scho)>0){
      $scho = Globals::result_XHEP($scho);
      foreach($scho as $sm){
        $filename = $content = $mime = '';
        list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_scholarship", $sm->id, $employeeid);
    ?>
      <tr id="<?= $sm->id ?>" table="employee_scholarship" style="border-top: 1px solid #ddd !important;">
          <!-- <td educscho='<?=$sm->ID?>'><?=$sm->level?></td> -->
          <td relscho="<?=$sm->type_of_scho?>"><?=$sm->type_of_scho?></td>
          <td><?=$sm->gr_agency?></td>
          <td><?=$sm->prog_study?></td>
          <td><?=$sm->ins_scho?></td>
          <td><?=$sm->datef?></td>
          <td><?=$sm->dateto?></td>
          <td>
              <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $sm->id ?>"  type="file" style="visibility:hidden; width: 20px" required="required"/>
          </td>
          <td class="tooltip" id="<?= $sm->id ?>" table="employee_scholarship">
              <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$sm->status?><span class="tooltiptext tooltiptext_<?=$sm->id?>_employee_scholarship">Loading..</span></a><?php } ?>
              <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?>"> <?=$sm->status?></a><?php } ?>
          </td>
          <td><?=$sm->dra_remarks ?></td>
          <td class="align_center">
            <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status != 'APPROVED')){ ?>
                <div id="btn_pos">
                <a class='btn btn-primary edit_scho' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_scho' tbl_id="<?=$sm->id?>"><i class='glyphicon glyphicon-trash'></i></a>
              </div>
            <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status=='APPROVED'){ ?>
              <div id="btn_pos">
                <a class='btn btn-primary edit_scho' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
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
  $("#schoinfolist").dataTable();

    $('#schoinfolist tbody').on('click', '.edit_scho', function () {
        addScho($(this), $(this).attr("tbl_id"));
    });

      $('#schoinfolist tbody').on('click', '.delete_scho', function () {
    var mtable = $("#schoinfolist").find("tbody");
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
    deleteScho($(this), $(this).attr("tbl_id"));
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


  $('#schoinfolist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#schoinfolist .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   