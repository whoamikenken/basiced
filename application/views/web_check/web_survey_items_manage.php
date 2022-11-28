<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
?>	
<div class="col-md-12" id="items">
	<form class="form-horizontal" id="web-survey-items-manage">
		<input type="hidden" name="action" value="<?= $tag ?>">
		<input type="hidden" name="id" value="<?= $id ?>">
		<div class="col-md-12">
			<div class="col-md-6">
				<div class="form-group">
				    <label class="col-sm-3 control-label">Category</label>
				    <div class="col-sm-9">
				    	<select class="form-control" name="category">
				    		<?php foreach ($categorySetup as $key => $value): ?>
				    			<?php if ($value["id"] == $category){ ?>
				    				<option value="<?= $value["id"] ?>" selected><?= $value['name'] ?></option>
				    			<?php }else{ ?>
				    				<option value="<?= $value["id"] ?>" ><?= $value['name'] ?></option>
			    				<?php } ?>
				    		<?php endforeach ?>
		                </select>
				    </div>
				</div>
			</div>
			<div class="col-md-6">
				<div class="form-group">
				    <label class="col-sm-3 control-label">Status</label>
				    <div class="col-sm-9">
				    	<select name="status" class="form-control">
				    		<option <?= ($status == "Active")? "selected":""; ?> value="Active">Link Web Checkin</option>
				    		<option <?= ($status == "In-Active")? "selected":""; ?> value="In-Active">Un-Link Web Checkin</option>
				    	</select>
				    </div>
				</div>
			</div>
		</div><br><br><br>
		<div class="col-md-12">
			<div class="form-group">
			    <label class="col-sm-3 control-label">Description</label>
			    <div class="col-sm-9">
			    	<input type="textarea" class="form-control" name="description" value="<?= $description ?>" rows="4">
			    </div>
			</div>
		</div><br><br>
		</form>
		<h3>Questions</h3>
		<?php if (count(explode("/", substr($questions, 1))) > 0){ ?>
			<?php foreach (explode("/", substr($questions, 1)) as $key => $value): 
				$info = explode("*", $value);
				// ADDING CHECKING
				if ($info[0] == "") $info[1] = "none";
				?>

				<div id="question" class="question">
					<div class="col-md-12" style="margin-bottom: 13px">
						<div class="col-md-5">
							<div class="form-group">
							    <label class="col-sm-4 control-label">Question</label>
							    <div class="col-sm-8">
							    	<input type="text" name="question" class="form-control" value="<?= $info[0]  ?>">
							    </div>
							</div>
						</div>
						<div class="col-md-5">
							<div class="form-group">
							    <label class="col-sm-4 control-label">Choices</label>
							    <div class="col-sm-8">
									<select class="form-control" name="choices">
										<option value="TEXT" <?= ($info[1] == "TEXT")? "selected":"" ?>>TEXT</option>
										<option value="NUMBER" <?= ($info[1] == "NUMBER")? "selected":"" ?>>NUMBER</option>
										<option value="YN" <?= ($info[1] == "YN")? "selected":"" ?>>Multiple Choice</option>
										<option value="TIME" <?= ($info[1] == "TIME")? "selected":"" ?>>TIME</option>
										<option value="DATE" <?= ($info[1] == "DATE")? "selected":"" ?>>DATE</option>
									</select>
							    </div>
							</div>
						</div>
						<div class="col-md-2">
							<a class="btn btn-info addbtn"><i class="glyphicon glyphicon-plus"></i></a>
						</div>
					</div>
				</div>
			<?php endforeach ?>
		<?php }else{ ?>
			<div id="question" class="question">
				<div class="col-md-12" style="margin-bottom: 13px">
					<div class="col-md-5">
						<div class="form-group">
						    <label class="col-sm-4 control-label">Question</label>
						    <div class="col-sm-8">
						    	<input type="text" name="question" class="form-control">
						    </div>
						</div>
					</div>
					<div class="col-md-5">
						<div class="form-group">
						    <label class="col-sm-4 control-label">Choices</label>
						    <div class="col-sm-8">
								<select class="form-control" name="choices">
									<option value="TEXT">TEXT</option>
									<option value="NUMBER">NUMBER</option>
									<option value="YN">Multiple Choice</option>
									<option value="TIME">TIME</option>
									<option value="DATE">DATE</option>
								</select>
						    </div>
						</div>
					</div>
					<div class="col-md-2">
						<a class="btn btn-info addbtn"><i class="glyphicon glyphicon-plus"></i></a>
					</div>
				</div>
			</div>
		<?php } ?>


		<!-- BLANK QUESTION -->
		<div id="blank" style="display: none;">
			<div class="col-md-12" style="margin-bottom: 13px">
				<div class="col-md-5">
					<div class="form-group">
					    <label class="col-sm-4 control-label">Question</label>
					    <div class="col-sm-8">
					    	<input type="text" name="question" class="form-control">
					    </div>
					</div>
				</div>
				<div class="col-md-5">
					<div class="form-group">
					    <label class="col-sm-4 control-label">Choices</label>
					    <div class="col-sm-8">
							<select class="form-control" name="choices">
								<option value="TEXT">TEXT</option>	
								<option value="NUMBER">NUMBER</option>
								<option value="YN">Multiple Choice</option>
								<option value="TIME">TIME</option>
								<option value="DATE">DATE</option>
							</select>
					    </div>
					</div>
				</div>
				<div class="col-md-2">
					<a class="btn btn-info addbtn"><i class="glyphicon glyphicon-plus"></i></a>&nbsp;&nbsp;<a class="btn btn-danger removebtn"><i class="glyphicon glyphicon-trash"></i></a>
				</div>
			</div>
			<br><br>	
		</div>
		<h3>Audience</h3>
		<div class="col-md-12">
			<div class="col-md-4">
				<div class="form-group">
				    <label class="col-sm-3 control-label">Type</label>
				    <div class="col-sm-9">
				    	<select class="chosen" id="type">
				    		<option value="">All</option>
				    		<option value="teaching">Teaching</option>
                            <option value="nonteaching">Non Teaching</option>
				    	</select>
				    </div>
				</div>
			</div>
			<div class="col-md-8">
				<div class="form-group">
				    <label class="col-sm-3 control-label">Employee</label>
				    <div class="col-sm-9">
				      <select class="chosen form-control" id="setupEmployeeSelect" multiple>
				      		<?php foreach ($employeeList as $key => $value): ?>
				    			<?php if (strpos($audience, $value["employeeid"]) !== false){ ?>
				    				<option value="<?= $value["employeeid"] ?>" selected><?= $value['fullname'] ?></option>
				    			<?php }else{ ?>
				    				<option value="<?= $value["employeeid"] ?>"><?= $value['fullname'] ?></option>
			    				<?php } ?>
				    		<?php endforeach ?>
			          </select>
				    </div>
				</div>
			</div>
		</div>
</div>

<script type="text/javascript">
	var count = 1;
	$(document).ready(function(){
		$(".chosen").chosen();
	});

	$("#items").delegate(".addbtn", "click", function() {
		if (count == 10) {
			Swal.fire({
		        icon: 'warning',
		        title: 'Warning!',
		        text: 'Maximum of 10 questions only.',
		        showConfirmButton: true,
		        timer: 1500
		    })
		    return false;
		}
		var blank = $("#blank").clone();
		$(blank).css("display","block");
		$(blank).addClass("question");
		$(blank).removeAttr( "id" );

        var data = $(this).parent().parent().parent().after(blank);
        count += 1;
    });

    $("#items").delegate(".removebtn", "click", function() {
        $(this).parent().parent().parent().remove();
        count -= 1;
    });

	$('#button_save_modal').unbind('click').bind('click', function (e) {
		var questions = "";
		$('.question').each(function(i, obj) {
			if ($(this).find("input[name='question']").val() != "") {
				questions += "/"+ $(this).find("input[name='question']").val()+"*"+$(this).find("select[name='choices']").val();
			}
		});
		
		if ($("input[name='description']").val() == "") {
			Swal.fire({
		        icon: 'warning',
		        title: 'Warning!',
		        text: 'Please Input Description',
		        showConfirmButton: true,
		        timer: 1500
		    })
			return false;
		}

		if ($("#setupEmployeeSelect").val() == null) {
			Swal.fire({
		        icon: 'warning',
		        title: 'Warning!',
		        text: 'Please Select Employee',
		        showConfirmButton: true,
		        timer: 1500
		    })
			return false;
		}
		
    	var formdata = $("#web-survey-items-manage").serialize()+ "&audience="+ $("#setupEmployeeSelect").val()+ "&questions="+ questions;
	        $.ajax({
	        type: "POST",
	        url: $("#site_url").val() + "/webcheckin_/saveSurveyItemsSetup",
	        data: formdata,
	        success:function(response){
	            if(response == "date"){
	                Swal.fire({
				        icon: 'warning',
				        title: 'Warning!',
				        text: 'Selected date has already existing transaction',
				        showConfirmButton: true,
				        timer: 1500
				    })
	            }else if(response == "added." || response == "updated."){
	            	Swal.fire({
				        icon: 'success',
				        title: 'Success!',
				        text: 'Setup is successfully '+ response,
				        showConfirmButton: true,
				        timer: 1500
				    })
				    setTimeout(function() {
				    	surveyItemsSetup();
	            		$('#modal-view').modal('toggle');
				    }, 1600);
	            }else{
	            	Swal.fire({
				        icon: 'warning',
				        title: 'Warning!',
				        text: response+' already have a setup.',
				        showConfirmButton: true,
				        timer: 10000
				    })
	            }
	            
	        }
	        });
	  
	});

	$("#type").change(function(){
	    employeeListManage($("#type").val());
	});

	function employeeListManage(type){
	    $.ajax({
	        url: $("#site_url").val() + "/webcheckin_/loadEmployeeListDropdownm",
	        type: "POST",
	        data:{type:type, selected:"itemsAll"},
	        success:function(response){
	        	console.log(response);
	            $("#setupEmployeeSelect").html(response);
	            $("#setupEmployeeSelect").trigger("chosen:updated");
	        }
	    });
	}

</script>