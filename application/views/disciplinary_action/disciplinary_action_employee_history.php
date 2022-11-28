<?php
	//Added (6-28-2017)
	$CI =& get_instance();
    $CI->load->model('disciplinary_action');
	$empid = $this->session->userdata("username");
	$query = $CI->disciplinary_action->getOffenseHistory($empid);
?>

<style>

input[name=mar]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}
   .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content">
	<div class="widgets_area">
		<div class="row">
			<div class="col-md-12">
				<div class="panel animated fadeIn">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Disciplinary Action History</b></h4></div>
                   <div class="panel-body">
                       <table class="table table-striped table-bordered table-hover" id="offbus">
							<thead>
								<tr style="background-color: #0072c6;">
									<th>Type of Offense</th>
									<th>Date of Violation</th>
									<th>Employer`s Statement</th>
									<!-- <th>Employee Statement</th> -->
									<th>Given Action</th>
									<th>Date of Warning</th>
									<th>Status</th>
								</tr>
							</thead>
							<tbody>
							<?
								foreach($query->result() as $row){
									foreach ($row as $key => $value) $row->$key = Globals::_e($value);
									?>
										<tr <?=($row->confirm == "NO")?"style='background-color:#fcf044'":""?>>
											<td><?=$row->offense?></td>
											<td><?= ($row->dateViolation == "0000-00-00") ? $this->extensions->getMonthDescription($row->month)." ".$row->year : $row->dateViolation; ?></td>
											<td><?=$row->employeers_statement?></td>
											<!-- <td><?=$row->employee_statement?></td> -->
											<td><?=$row->sanction?></td>
											<td><?=$row->dateWarning?></td>
											<td><?=($row->confirm == "NO")?"<a class='btn tag' action='tag' style='color:white;background-color:red' data='".$row->id."'>Tag as Confirmed</a>":"CONFIRMED"?></td>
										</tr>
									<?
								}
							?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(".tag").unbind("click").bind("click",function(){
		var id = $(this).attr("data");
		$.ajax({
			url: "<?=site_url('disciplinary_action_/confirmAction')?>",
			type: "POST",
			data: {id:id},
			success: function(msg){
				if(msg == "Something Went Wrong!"){
					Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: msg,
                      showConfirmButton: true,
                      timer: 1000
                    })
				}else{
					Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: msg,
                      showConfirmButton: true,
                      timer: 1000
                    })
				}
				document.location.reload();
			}
		}); 
	});
	
	$(document).ready(function(){
		if("<?=$menuid_selected?>" == 119)
		{
			if($("a[action='tag']").length > 0)
			{
				$("#changePasswordDiv").hide();
			}
		}
	});
	
$(document).ready(function(){
    var table = $('#offbus').DataTable({
    });
    new $.fn.dataTable.FixedHeader( table );
});
</script>
