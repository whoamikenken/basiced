<?php
$CI =& get_instance();
    $CI->load->model('leave_application');
	 $pts_pdp1 = $this->db->query("SELECT DISTINCT b.level AS title, b.level AS venue, a.id as id, a.leave_id, a.is201, a.venue as venue_id, a.title as title_id, a.datef, a.organizer, a.status, a.datet, a.seminar_title, a.location, a.regfee, a.transfee, a.accfee, a.total, a.dra_remarks FROM employee_pts_pdp1 a LEFT JOIN reports_item b ON b.level = a.venue WHERE employeeid='{$employeeid}' ORDER BY a.datef DESC")->result();

	if(count($pts_pdp1)>0){
        $pts_pdp1 = Globals::result_XHEP($pts_pdp1);
        foreach($pts_pdp1 as $xm){
          $filename = $content = $mime = '';
          list($filename, $content, $mime) = $this->extensions->getEmployee201Files("employee_pts_pdp1", $xm->id, $employeeid);
          if($xm->is201 == "NO") list($filename, $content, $mime) = $CI->leave_application->getSeminarFiles($xm->leave_id);
          if($xm->seminar_title){
?>
        <tr id="<?= $xm->id ?>" table="employee_pts_pdp1" style="border-top: 1px solid #ddd !important;">
            <!-- <td reltitle="<?=$xm->title_id?>" style="display: none;"><?=$xm->title_id?></td> -->
            <td><?=$xm->seminar_title?></td>
            <td><?=$xm->location?></td>
            <td><?=$xm->datef?></td>
            <td><?=$xm->datet?></td>
            <td><?=$xm->organizer?></td>
            <!-- <td relvenue="<?=$xm->venue_id?>"><?=$xm->venue_id?></td>
            <td><?=$xm->regfee?></td>
            <td><?=$xm->transfee?></td>
            <td><?=$xm->accfee?></td>
            <td><?=$xm->total?></td> -->
            <td>
                <a class="filename" content="<?= $content ?>" mime="<?= $mime ?>"><?= Globals::_e($filename) ?></a><input class="myInput" id="<?= $xm->id ?>"  type="file" style="visibility:hidden; width: 20px" required="required"/>
            </td>
            <td class="tooltip" id="<?= $xm->id ?>" table="employee_pts_pdp1">
                <?php if($this->session->userdata("usertype") == "ADMIN"){ ?> <a class="btn <?= $xm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?> update_status"> <?=$xm->status?><span class="tooltiptext tooltiptext_<?=$xm->id?>_employee_pts_pdp1">Loading..</span></a><?php } ?>
                <?php if($this->session->userdata("usertype") == "EMPLOYEE"){ ?><a class="btn <?= $xm->status=='APPROVED' ? 'btn-success' : 'btn-danger'?>"> <?=$xm->status?></a><?php } ?>
            </td>
            <td><?=$xm->dra_remarks ?></td>
            <td class="align_center">
              <?php if ($this->session->userdata("usertype") == "ADMIN" || ($this->session->userdata("usertype") == "EMPLOYEE" && $xm->status != 'APPROVED')){ ?>
                  <div id="btn_pos">
                  <a class='btn btn-primary edit_pts_pdp1' tbl_id="<?=$xm->id?>" href='#modal-view' data-toggle='modal' ><i class='glyphicon glyphicon-edit'></i></a><a class='btn btn-warning delete_pts_pdp1' tbl_id="<?=$xm->id?>"><i class='glyphicon glyphicon-trash'></i></a>
                </div>
              <?php }else if($this->session->userdata("usertype") == "EMPLOYEE" && $xm->status=='APPROVED'){ ?>
                <div id="btn_pos">
                  <a class='btn btn-primary edit_pts_pdp1' tbl_id="<?=$xm->id?>" href='#modal-view' data-toggle='modal' ><i class='glyphicon glyphicon-edit'></i></a>
                </div>
              <?php } ?>
            </td>
        </tr>    
<?                      }      
        }
   }
?>
<script type="text/javascript">
	$("#pts_pdp1infolist").dataTable();

	$('#pts_pdp1infolist tbody').on('click', '.edit_pts_pdp1', function () {
        addpts_pdp1($(this), $(this).attr("tbl_id"));
    });

	$('#pts_pdp1infolist tbody').on('click', '.delete_pts_pdp1', function () {
	    var mtable = $("#pts_pdp1infolist").find("tbody");
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
	        deletepts_pdp1($(this), $(this).attr("tbl_id"));
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

	$('#pts_pdp1infolist tbody').on('click', '.update_status', function () {
	    var current_status = $(this).text();
	    var table = $(this).closest("tr").attr("table");
	    var id = $(this).closest("tr").attr("id");
	    var status = updateTableStatus(table, id);
	    $(this).html(status + " <span class='tooltiptext tooltiptext_"+id+"_"+table+"'>"+status+"</span>")
	    if(status == 'APPROVED') $(this).removeClass("btn-danger").addClass("btn-success");
	    else $(this).removeClass("btn-success").addClass("btn-danger");
	});

	$("#pts_pdp1infolist .tooltip").hover(function(){
	    var id = $(this).attr('id');
	    var table = $(this).attr('table');
	    loadStatusHistory(id, table);
	});
</script>                   