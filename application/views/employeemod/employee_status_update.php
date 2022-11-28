<?
	//Added 5/8/17
	$employeestatus = $this->employeemod->employeestatusupdatenotif()->result();
	
?>
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<style>@media (max-width: 768px) { .elfinderimg{   display: none;  }   } .error{color: red;}</style>
<div id="content"> <!-- Content start -->
	<div class="widgets_area">
		<div class="row">
			<form id="employee_status_update">
				<div class="col-md-12">
				   <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Employee Status</b></h4></div>
                   <div class="panel-body">
						<?
							if($employeestatus) $employeestatus = Globals::result_XHEP($employeestatus);
							foreach($employeestatus as $row)
							{
								$duration = $row->duration;
								$content = $this->employeemod->employeestatusupdatenotifcontent($row->code, $row->duration);
								// echo "<pre>";print_r($this->db->last_query());die;
								?>
								<div id="<?=strtolower($row->description)?>">
									<div class="well">
										<div class="field">
											<h3><?=$row->description?>
											<?
												if($content->num_rows() && $row->duration > 0)
												{
													echo "
													<div class='notifdiv'><i class='glyphicon glyphicon-bell large' style='color:black'></i><span class='notifcount'><b>".$content->num_rows()."</b></span></div>
													";
												}
											?>
											</h3>
										</div>
										<table class="table table-striped table-bordered table-hover" id="table">
											<thead style="background-color: #0072c6;">
												<tr>
													<th>#</th>	
													<th>Employee ID</th>	
													<th>Employee Name</th>	
													<th>Department</th>	
													<th>Office</th>	
													<th>Position</th>	
													<th>Start Date</th>	
													<th>Edit</th>	
												</tr>
											</thead>
											<tbody id="<?=strtolower($row->description)?>table">
												<?
													$i = 0;
													if($content)
													{
														foreach(Globals::result_XHEP($content->result()) as $r)
														{
															$i++;
															?>
															<tr>
																<td><?=$i?></td>
																<td><?=$r->employeeid?></td>
																<td><?=$r->lname?>, <?=$r->fname?> <?=$r->mname?></td>
																<td><?=$this->setup->getDepartmentDesc($r->deptid)?></td>
																<td><?=$this->extras->getDeptDesc($r->office)?></td>
																<td><?=$this->extras->showPosDesc($r->positionid)?></td>
																<td><?=$r->dateposition?></td>
																<td class="align_center"><a class='btn btn-info editEmpStat' name="<?=$r->lname?>, <?=$r->fname?> <?=$r->mname?>" id="<?=$r->employeeid?>" mgmt="<?=$r->managementid?>" dept="<?=$r->deptid?>" officeid="<?=$r->office?>" estat="<?=$r->employmentstat?>" dateres="<?=$r->dateresigned?>" pos="<?=$r->positionid?>" datepos="<?=$r->dateposition?>" reason="<?=$r->resigned_reason?>" duration="<?=$duration?>" href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a></td>
															</tr>
															<?
														}
													}
												?>												
											</tbody>
										</table>
									</div>
								</div>
								<?
							}
						
						?>
					</div>
				</div>
			</form>
		</div>
	</div>
</div>

<script>
	var toks = hex_sha512(" ");
	if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
    else $(".btn").css("pointer-events", "");
	$(document).ready(function(){
	    var table = $('table').DataTable({
	    });
	    new $.fn.dataTable.FixedHeader( table );
	});
	$(".editEmpStat").click(function(){
		var employeeid      = $(this).attr('id'),
			name     		 = $(this).attr('name'),
			management      = $(this).attr('mgmt'),
			deptid          = $(this).attr('dept'),
			officeid          = $(this).attr('officeid'),
			employmentstat  = $(this).attr('estat'),
			position        = $(this).attr('pos'),
			datepos         = $(this).attr('datepos'),
			dateres         = $(this).attr('dateres'),
			reason         = $(this).attr('reason'),
			duration         = $(this).attr('duration');
			
		$("#modal-view").find("h3[tag='title']").text("Edit Employment Status");
		$("#button_save_modal").text("Save");
		var form_data = {
			employeeid:  GibberishAES.enc(employeeid , toks),
			name:  GibberishAES.enc(name , toks),
			management:  GibberishAES.enc(management , toks),
			deptid:  GibberishAES.enc(deptid , toks),
			officeid:  GibberishAES.enc(officeid , toks),
			employmentstat:  GibberishAES.enc( employmentstat, toks),
			position:  GibberishAES.enc(position , toks),
			datepos:  GibberishAES.enc(datepos , toks),
			dateres:  GibberishAES.enc( dateres, toks),
			reason:  GibberishAES.enc(reason , toks),
			duration:  GibberishAES.enc(duration , toks),
			folder : GibberishAES.enc("employeemod"  , toks), 
			page   : GibberishAES.enc("editEmpStatModal"  , toks),
			toks:toks
		};
		$.ajax({
			url: "<?=site_url('employee_/viewModal')?>",
			type: "POST",
			data: form_data,
			success: function(msg){
				$("#modal-view").find("div[tag='display']").html(msg);
			}
		});  
	});
</script>