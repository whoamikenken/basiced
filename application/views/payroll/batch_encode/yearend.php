<?php

	$CI =&get_instance();
	$CI->load->model('utils');

?>
<input type="hidden" id="function" value="<?=isset($function) ? $function : ''?>">
<div class="modal-dialog modal-md">

	<div class="modal-content" >
		<div class="modal-header" >
			<div class="media">
				<div class="media-left">
					<img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
				</div>
				<div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
					<h4 class="media-heading" ><b>Pinnacle Technologies Inc.</b></h4>
					<p style="font-family:Avenir; margin-top: -1%;">D`Great</p>
				</div>
			</div>
			<center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Batch Process</h3></b></center>
		</div>
		<div class="modal-body">
			<div class="row">
		        <form class="form-horizontal" id="year-end-rep">

		        	<div class="form-group">
						<label class="control-label col-sm-3">Category:</label>
						<div class="col-sm-8">
							<select class="form form-control chosen" name="income_category" id="income_category">
								<?php foreach($income_config as $row): ?>
									<option value="<?=$row['id']?>"><?=$row['description']?></option>
								<?php endforeach ?>
                            </select>
						</div>
					</div>

		        	<div class="form-group">
						<label class="control-label col-sm-3" for="deptid">Department:</label>
						<div class="col-sm-8">
							<select class="chosen batchprocess_filter" name="deptid">
								<?=$this->extras->getDeptpartment()?>
							</select>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-3" for="office">Office:</label>
						<div class="col-sm-8">
							<select class="chosen batchprocess_filter" name="office">
								<?=$this->extras->getOffice()?>
							</select>
						</div>
					</div>

					<div class="form-group is_regdeduc">
						<label class="control-label col-sm-3" for="date" id="datelabel"> Date:</label>
						<div class="col-sm-8">
							<div class='input-group date' data-date="" data-date-format="yyyy-mm-dd">
                                <input type='text' class="form-control dateset" size="16" name="dateset" type="text" value=""/>
                                <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
						</div>
					</div>
					<div class="form-group" style="display: none;">
						<label class="control-label col-sm-3" for="date">End Date:</label>
						<div class="col-sm-8">
							<div class='input-group date' data-date="" data-date-format="yyyy-mm-dd">
								<input type='text' class="form-control dateto" size="16" name="dateto" type="text" value=""/>
								<span class="input-group-addon">
								<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>

					<div class="form-group">
						<label class="control-label col-sm-3" for="employeeid">Employee:</label>
						<div class="col-sm-8">
							<select class="chosen col-md-6" id="employeeid" name="employeeid[]" multiple="">
								<option value="">All  employee</option>
	                            <?php foreach($emplist as $row): ?>
	                            	<option value="<?=$row['employeeid']?>"><?=$row['employeeid']." - ".$row['fullname']?></option>
	                            <?php endforeach ?>
	                        </select>&nbsp;&nbsp;
						</div>
					</div>

					<div class="form-group is_regdeduc">
						<label class="control-label col-sm-3">No. of Cut-Off/s:</label>
						<div class="col-sm-8">
							<input type="text" class="form-control" name="process_nocutoff" placeholder="Enter no cut-off">
						</div>
					</div>
					
					<div class="form-group">
						<label class="control-label col-sm-3">Quarter Schedule:</label>
						<div class="col-sm-8">
							<select class="chosen" name="rep_quarter">
								<option value="1">1st Cut-off</option>
								<option value="2">2nd Cut-off</option>
								<option value="3">all Cut-off</option>
							</select>
						</div>
					</div>
				</form>
		    </div>
		</div>
		<div class="modal-footer">
			<div id="batchsaving">
				<button type="button" class="btn btn-success" id='gen_inc_rep'>Generate</button>
				<button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
			</div>
			<div id="batchloading" style="display: none;"><img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.</div>
		</div>
	</div>

</div>
<!-- <div id="loading"><img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait.</div> -->
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<input type="hidden" id="username" value="<?= $this->session->userdata('username') ?>">
<script src="<?=base_url()?>js/batch_encode/be_main.js"></script>
<script>
	var toks = hex_sha512(" ");

	$("#employee").on("change", function(){
	    var elementId = $(this).attr("id");
	    var value = $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").show();});
	});

    $("select[name='deptid']").change(function(){
	    $.ajax({
	        url: $("#site_url").val() + "/setup_/getOffice",
	        type: "POST",
	        data: {department: GibberishAES.enc( $(this).val(), toks), toks:toks},
	        success: function(msg){
	            $("#year-end-rep").find("select[name='office']").html(msg).trigger("chosen:updated");
	        }
	    });
	});

    $('.batchprocess_filter').on('change',function(){
	    var campus = GibberishAES.enc($("#year-end-rep").find("select[name='campus']").val(), toks);
	    var teachingType = GibberishAES.enc($("#year-end-rep").find("select[name='teachingtype']").val(), toks);
	    var employmentstat = GibberishAES.enc($("#year-end-rep").find("select[name='employmentstat']").val(), toks);
	    var office = GibberishAES.enc($("#year-end-rep").find("select[name='office']").val(), toks);
	    var department = GibberishAES.enc($("#year-end-rep").find("select[name='deptid']").val(), toks);
	    var status = GibberishAES.enc($("#year-end-rep").find("select[name='status']").val(), toks);
	    $.ajax({
	        type : "POST",
	        url: "<?=site_url('employee_/load201sort')?>",
	        data: {campus: campus, teachingType:teachingType, department:department, status:status, office:office, employmentstat:employmentstat,toks:toks},
	        success: function(data){
	            $("select[name='employeeid[]']").html(data).trigger("chosen:updated");
	        }
	    });
	});

	$("#gen_inc_rep").click(function(){
		$("#year-end-rep").attr("action", "<?=site_url('reports_/generateYearEndReport')?>");
		$("#year-end-rep").attr("target", "_blank");
		$("#year-end-rep").attr("method", "POST");
		$("#year-end-rep").submit();
	});

    $(".chosen").chosen();

</script>