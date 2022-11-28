<?php
	//Added (6-2-2017)
?>

<style>
.dataTables_paginate {
    margin-top: 6px;
}
.datatable tr th{
    padding: 1px 12px 1px 12px;
}
#offbus tr td,#offbus tr th{
    text-align: center;
}
input[name=mar]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}

.tdwidth{
  width: 100px;
  overflow: hidden;
  display: inline-block;
  white-space: nowrap;
}
.thwidth{
  width: 78px;
  overflow: hidden;
}
</style>
<table class="table table-hover table-bordered" id="offbus">
    <thead style="background-color: #0072c6;">
        <tr>
            <th class="thwidth">&nbsp;</th>
            <th>Type of Offense</th>
            <th>Date of Violation</th>
            <th>Employer`s Statement</th>
            <th>Employee Statement</th>
            <th>Given Action</th>
			<th>Date of Warning</th>
			<th>Status</th>
        </tr>
    </thead>
    <tbody>
    	<?
        if($d_list->num_rows() > 0){
        	foreach ($d_list->result() as $key => $row) {
				?>
        		<tr>
						<?if($row->confirm == "NO"){?>
              <td class="tdwidth">
                <a class="btn btn-info editbtn" href="#" data-toggle="modal" data-target="#myModal" idkey="<?=$row->id?>"><i class="icon glyphicon glyphicon-edit"></i></a>&nbsp;&nbsp;
                <a class="btn btn-danger delbtn" href="#" idkey="<?=$row->id?>" ><i class="icon glyphicon glyphicon-trash"></i></a>
              </td>
						<?}else{?>
              <td></td>
            <?}?>
					<td width="20%;"><?=$row->offense?></td>
					<td><?= ($row->dateViolation == "0000-00-00") ? $this->extensions->getMonthDescription($row->month)." ".$row->year : $row->dateViolation; ?></td>
					<td><?=$row->employeers_statement?></td>
					<td><?=$row->employee_statement?></td>
					<td><?=$row->sanction?></td>
					<td><?=$row->dateWarning?></td>
					<td><?=($row->confirm == "NO")?"Not yet confirmed":"Confirmed"?></td>
				</tr>
            <?}
        }
        ?>
    </tbody>
</table>

<div id="delete-alert" class="hide">
    <div><h5>Are You sure you want to delete <span id="chosen-row" class="text-error"></span> ?</h5></div>
    <div>
        <input type="hidden" class="hiddenid" />
        <a href="#" class="btn dark_green" id="del-submit">Yes</a>
        <a href="#" class="btn btn-danger del-close" data-dismiss="modal">No</a>
    </div>
</div>

<script type="text/javascript">
  var toks = hex_sha512(" ");
$(".editbtn").click(function(){
    var idkey = $(this).attr('idkey');

    $.ajax({
       url      :   "<?=site_url("disciplinary_action_/getEmpOffenseDetails")?>",
       type     :   "POST",
       dataType :   "json",
       data     :   {def_id: GibberishAES.enc(idkey , toks), toks:toks},
       success  :   function(data){
			$('#dateWarning2').val(data.dateWarning);
			$('#offense').val(data.offense_code).trigger("chosen:updated");
			$('#dateViolation2').val(data.dateViolation);
			$('#employeersStatement').val(data.employeers_statement);
            $('#empStatement').val(data.employee_statement);
            $('#sanction').val(data.sanction_code).trigger("chosen:updated");

            $("#edit").attr('idkey',idkey);
            $("#save").hide();
            $("#edit, #cancelEdit").show();
       },
       error : function(XMLHttpRequest, textStatus, errorThrown) { 
            console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
        }      
    });

});

$(".delbtn").click(function(){
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
            var id = $(this).attr("idkey");
            $.ajax({
              url: "<?=site_url('disciplinary_action_/deleteEmpOffense')?>",
              type: "POST",
              data: {id: GibberishAES.enc(id , toks), toks:toks},
              success: function(msg){
                Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: 'Employee Offense has been deleted successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })
                loadOffenseHistory($('#employeeid').attr('employeeid'));
              }
            }); 
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

$("#offbus").dataTable({
    "sPaginationType": "full_numbers",
    "oLanguage": {
                     "sEmptyTable":     "No Data Available.."
                 },
    "aLengthMenu": [[5, 10, 20, -1], [5, 10, 20, "All"]]
});
$('.chosen').chosen();
</script>
