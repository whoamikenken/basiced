<div class="form_row">
	<div>
		<table class="table table-striped table-bordered table-hover" id="ovetime_table">
			<thead style="background-color: #0072c6;">
				<tr>
					<th class="align_center" width="10%">#</th>
					<th class="align_center" width="20%">Employee Status Code</th>
					<th class="align_center" width="50%">Employee Status</th>
					<th class="align_center" width="20%"></th>
				</tr>
			</thead>
			<tbody>
			<?
				$count = 1;
				foreach ($overtime_list as $code => $description) {
			?>
				<tr>
					<td class="align_center"><?=$count?></td>
					<td class="align_center"><?=$code?></td>
					<td class="align_center"><?=$description?></td>
					<td class="align_center">
						<button class="btn btn-info" tag="edit" value="<?=$code?>"><span class="glyphicon glyphicon-edit"></span></button>
						<button class="btn btn-danger delbtn" tag="delete" id="<?=$code?>" count="<?=$count?>"><span class="glyphicon glyphicon-trash"></span></button>
					</td>

				</tr>
			<?
					$count += 1;
				}
			?>
			</tbody>
		</table>
	</div>
</div>

<div id="delete-alert" class="hide">
    <div style="text-align: center;"><h5>&nbsp;&nbsp;&nbsp;Are You sure you want to delete <b><span id="chosen-row" class="text-error"></span></b>?</h5></div>
</div>
<div id="delete-alert-footer" class="hide">
    <input type="hidden" class="hiddenid" />
    <a href="#" class="btn btn-danger modalclose" data-dismiss="modal">No</a>
    <a href="#" class="btn btn-success deletedata" id="del-submit" value="<?=isset($code) ? $code : '' ?>">Yes</a>
</div>
<script type="text/javascript">
 var toks = hex_sha512(" ");
 $('#ovetime_table').DataTable();

$("button[tag='edit']").unbind('click').click(function(){
	var code = this.value;

	$.ajax({
		url : "<?=site_url("overtime_/editOvertimeSetup")?>",
		type : "POST",
		data : { 
			toks:toks,
			code:GibberishAES.enc(code, toks) 
		},
		dataType : "json",
		success : function(result){
			showOvertimeSetup(code, result);
			
		}
	});
});

$(".delbtn").unbind().click(function(){
        var id = $(this).attr("id");
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
            $.ajax({
				url : "<?=site_url("overtime_/deleteOvertimeSetup")?>",
				type : "POST",
				data : { 
					toks:toks,
					code:GibberishAES.enc(id, toks) 
				},
				success : function(result){
					loadOvertimeSetupList();
					Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: 'Overtime Rate has been deleted successfully.',
                          showConfirmButton: true,
                          timer: 1000
                      })
					
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

$(document).unbind("click").on("click", "#del-submit", function(){
	$(".modalclose").click();
	
	var code = $(this).attr("tagkey");
	$.ajax({
		url : "<?=site_url("overtime_/deleteOvertimeSetup")?>",
		type : "POST",
		data : { 
			toks:toks, 
			code:GibberishAES.enc(code, toks) 
		},
		success : function(result){
			loadOvertimeSetupList();
			alert("Successfully Deleted Overtime Setup!");
			
		}
	});
});

if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
else $(".btn").css("pointer-events", "");

</script>
