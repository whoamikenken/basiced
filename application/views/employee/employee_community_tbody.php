<?php
	$community   = $this->db->query("SELECT e.educational_level, r.level, e.id as id, e.school, e.ctype, e.honor, e.year_grad, e.status, e.dra_remarks from employee_community e LEFT JOIN reports_item r ON e.educational_level = r.ID  where employeeid='{$employeeid}'")->result();

	if(count($community)>0){
    $community = Globals::result_XHEP($community);
    foreach($community as $sm){
      $filename = $content = $mime = '';
list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_community", $sm->id, $employeeid);
?>
    <tr id="<?= $sm->id ?>" table="employee_community"  style="border-top: 1px solid #ddd !important;">
        <td><?=$sm->school?></td>
        <!--<td releduclevel="<?=$sm->educational_level?>"><?=$sm->educational_level?></td>-->
        <td><?=$sm->year_grad?></td>
        <td><?=$sm->honor?></td>
        <td>
            <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $sm->id ?>"  type="file" style="visibility:hidden; width: 20px" required="required"/>
        </td>
        <td class="tooltip" id="<?= $sm->id ?>" table="employee_community">
            <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$sm->status?><span class="tooltiptext tooltiptext_<?=$sm->id?>_employee_community">Loading..</span></a><?php } ?>
            <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $sm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?>"> <?=$sm->status?></a><?php } ?>
        </td>
        <td><?=$sm->dra_remarks?></td>
        <td class="align_center">
      

          <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status != 'APPROVED')){ ?>
              <div id="btn_pos">
              <a class='btn btn-primary edit_community' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_community' tbl_id="<?=$sm->id?>"><i class='glyphicon glyphicon-trash'></i></a>
            </div>
          <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $sm->status=='APPROVED'){ ?>
            <div id="btn_pos">
             <a class='btn btn-primary edit_community' tbl_id="<?=$sm->id?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>
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
  $("#communityinfolist").dataTable();

     $('#communityinfolist tbody').on('click', '.edit_community', function () {
        addCommunity($(this), $(this).attr("tbl_id"));
    });

    $('#communityinfolist tbody').on('click', '.delete_community', function () {
    var mtable = $("#communityinfolist").find("tbody");
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
        // if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
        // $(this).parent().parent().parent().remove();
        deleteCommunity($(this), $(this).attr("tbl_id"));
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

  $('#communityinfolist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#communityinfolist .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   