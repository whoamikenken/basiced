<?
	$CI = &get_instance();
	$CI->load->model("service_credit");
	$query = $CI->service_credit->getEmpSC();
?>
<style>
/*.datatable thead tr th{*/
</style>
<div class="row">
    <div class="col-md-10 col-md-offset-1" >
        <table class="table table-hover table-bordered datatable" id="ldetails" >
            <thead >
                <tr >
                    <th class="align_center">Leave Type</th>
                    <th class="align_center">Total Service Credit</th>
                    <th class="align_center">Used Service Credit</th>
                    <th class="align_center">Available Service Credit</th>
                </tr>
            </thead>
            <tfoot>
                <tr>
                    <td colspan='4'>
						<a class="btn btn-primary" id="applysc" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">Apply Service Credit</a>
						<a class="btn btn-primary" id="usesc" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">Use Service Credit</a>
					</td>
                </tr>
            </tfoot>
            <tbody>
				<tr>
                    <td>SERVICE CREDIT</td>
                    <?
						if($query)
						{
							$total_sc = 0;
							$used_sc = 0;
							$available_sc = 0;
							foreach($query as $row)
							{
								$total_sc += $row->total_sc;
								$used_sc += $row->used_sc;
								$available_sc += $row->available_sc;
							}
							
							?>
							<td class="align_center"><?=$total_sc?></td>
							<td class="align_center"><?=$used_sc?></td>
							<td class="align_center"><?=$available_sc?></td>
							<?
						}
						else
						{
							?>
								<td colspan="3" class="align_center"><i>No Data Exists..</i></td>
							<?
						}
					?>
				</tr>
            </tbody>
        </table>
    </div>
</div>
<script>
	$("#applysc").click(function(){  
		$.ajax({
			url      : "<?=site_url("employeemod_/fileconfig")?>",
			type     : "POST",
			data     : {folder: "employeemod", view: "scapply"},
			success: function(msg){
				$("#myModal").html(msg);
			}
		});
	});
	
	$("#usesc").click(function(){  
		$.ajax({
			url      : "<?=site_url("employeemod_/fileconfig")?>",
			type     : "POST",
			data     : {folder: "employeemod", view: "scapplyuse"},
			success: function(msg){
				$("#myModal").html(msg);
			}
		});
	});
</script>