<style type="text/css">
	.form-row{
		padding-bottom: 15px;
	}
	
	.default{
		width:160px !important;
	}
</style>
<?php
	if(isset($existingData[0]['attendees'])) $attendees = explode(',', $existingData[0]['attendees']);
	else $attendees = array();

	if(isset($existingData[0]['attendeesDept'])) $attendeesDept = explode(',', $existingData[0]['attendeesDept']);
	else $attendeesDept = array();

	if(isset($existingData[0]['attendeesOffice'])) $attendeesOffice = explode(',', $existingData[0]['attendeesOffice']);
	else $attendeesOffice = array();

	if(isset($existingData[0]['employees'])) $employees = $existingData[0]['employees'];
	else $employees = '';

	// echo "<pre>"; print_r($existingData); die;

	$opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")), "", "", "", "", "", 'active');	
?>
<div class="col-md-12">
	<div class="col-md-12">
		<form id="inhouse_form">
			<input type="hidden" id="table_ID" name="id" value="">
			<input type="hidden" id="savedemployees" name="id" value="<?=$employees?>">
			<div class="form-row col-md-12" id="usernameDiv">
				<div class="field-label col-md-3">
					<label>Username</label>
				</div>
				<div class="field col-md-9">
					<input type="input" class="form form-control" name="username" id="username" value="<?= (isset($existingData[0]['username'])) ? $existingData[0]['username'] : ''; ?>">
				</div>
			</div>

			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Password</label>
				</div>
				<div class="field col-md-9">
					<input type="password" class="form form-control" name="password" id="password" value="">
				</div>
			</div>
			<!--<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Title</label>
				</div>
				<div class="field col-md-9">
					<input type="input" class="form form-control" name="title" id="title">
				</div>
			</div>-->
			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Seminar Category</label>
				</div>
				<div class="field col-md-9">
					<div class="field no-search">
	                    <select class="form-control chzn-select" name="category" id="category" placeholder="Category">
	                        <option value=""> - Select Seminar Category - </option>
                            <?php
                                $seminarList = Globals::seminarList();
                                foreach($seminarList as $c=>$val){
                                    ?><option value="<?=$c?>" <?= (isset($existingData[0]['category']) && $existingData[0]['category']==$c) ? "selected" : "" ?> ><?=$val?></option><?    
                                }
                            ?>
	                    </select>
	                </div>
				</div>
			</div>
			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Seminar Title</label>
				</div>
				<div class="field col-md-9">
					<div class="field no-search">
	                    <select class="form-control chzn-select" name="workshop" id="workshop" placeholder="Workshop">
	                    	<!-- <?php foreach($workshop_list as $key => $value):?>
	                            <option value="<?=$key?>"><?=$value?></option>
	                        <?php endforeach ?> -->
	                        <option value=""> - Select Seminar Title - </option>
	                    </select>
	                </div>
				</div>
			</div>
			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Date From</label>
				</div>
				<div class="field col-md-9">
					<div class='input-group date' id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 60%;">
	                    <input type='text' class="form-control" size="16" name="date_from" id="dfrom" type="text" value="<?= (isset($existingData[0]['date_from'])) ? $existingData[0]['date_from'] : $datetoday; ?>" style="pointer-events: none;"/>
	                    <span class="input-group-addon">
	                    	<span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
				</div>
			</div>
			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Date To</label>
				</div>
				<div class="field col-md-9">
					<div class='input-group date' id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 60%;">
	                    <input type='text' class="form-control" size="16" name="date_to" id="dfrom" type="text" value="<?= (isset($existingData[0]['date_to'])) ? $existingData[0]['date_to'] : $datetoday; ?>" style="pointer-events: none;"/>
	                    <span class="input-group-addon">
	                    	<span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
				</div>
			</div>
			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Time Start</label>
				</div>
				<div class="field col-md-9">
					<div class='input-group time' style="width: 60%;">
	                    <input type='text' class="form-control" name="time_from" id="time" value="<?= (isset($existingData[0]['time_from'])) ? $existingData[0]['time_from'] : ''; ?>" style="pointer-events: none;" />
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-time"></span>
	                    </span>
	                </div>
				</div>
			</div>
			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Time End</label>
				</div>
				<div class="field col-md-9">
					<div class='input-group time' style="width: 60%;">
	                    <input type='text' class="form-control" name="time_to" id="time" value="<?= (isset($existingData[0]['time_to'])) ? $existingData[0]['time_to'] : ''; ?>" style="pointer-events: none;"/>
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-time"></span>
	                    </span>
	                </div>
				</div>
			</div>

			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Organizer</label>
				</div>
				<div class="field col-md-9">
	                <input type='text' class="form-control" name="organizer" id="organizer" value="<?= (isset($existingData[0]['organizer'])) ? $existingData[0]['organizer'] : ''; ?>" />
				</div>
			</div>

			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Venue</label>
				</div>
				<div class="field col-md-9">
	                <select class="form-control chzn-select" name="venue" id="venue" required>
		                <?php foreach($venLevel as $ven => $venue):?>
		                    <option <?=(isset($existingData[0]['venue']) && $existingData[0]['venue']==$ven ? " selected" : "")?> value="<?= $ven ?>"><?= $venue ?></option>
		                <?php endforeach;?>
				     </select> 
				</div>
			</div>
<!-- 
			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Location</label>
				</div>
				<div class="field col-md-9">
	                <input type='text' class="form-control" name="location" id="location" value="<?= (isset($existingData[0]['location'])) ? $existingData[0]['location'] : ''; ?>" />
				</div>
			</div> -->

			<!-- <div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Attendees</label>
				</div>
				<div class="field col-md-9">
	                <select class="form-control chzn-select" name="attendees[]" id="attendees"  multiple>
		                <?= Globals::employmentYearList($attendees); ?>
				     </select> 
				</div>
			</div> -->

			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Attendees Department</label>
				</div>
				<div class="field col-md-9">
	                <select class="form-control chzn-select" name="attendeesDept[]" id="attendeesDept"  multiple>
		                <option value="all">All Department</option>
                        <?
                            $opt_department = $this->extras->showdepartment();
                            foreach($opt_department as $c=>$val){
                            ?>      <option value="<?=$c?>" <?=(in_array($c, $attendeesDept) ? 'selected' : '')?>><?=$val?></option><?
                            }
                        ?>
				     </select> 
				</div>
			</div>

			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Attendees Office</label>
				</div>
				<div class="field col-md-9">
	                <select class="form-control chzn-select" name="attendeesOffice[]" id="attendeesOffice"  multiple>
		                <option value="all" <?= ($attendeesOffice[0] == "all" ? 'selected' : '')?>>All Office</option>
                        <?php 
                        $opt_department = $this->extras->showoffice();
                        foreach($opt_department as $c=>$val): 
                        	if(count($attendeesOffice) > 0){
                        		if(in_array($c, $attendeesOffice)){
                        			?>
                            		<option value="<?=$c?>" selected><?=$val?></option>
                        			<?php
	                        	}
	                        }else{
	                        	?>
	                        		<option value="<?=$c?>"><?=$val?></option>
	                        	<?php
	                        }
                         endforeach ?>
				     </select> 
				</div>
			</div>

			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Employees</label>
				</div>
				<div class="field col-md-9">
	                <select class="form-control chzn-select" name="employees[]" id="employees"  multiple>
		                <option value="all">Select All Employee</option>
			                <?php
			                	foreach($opt_type as $val){
			                        ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . " " . $val['fname'] . " " . $val['mname'])?></option><?    
			                    }
			                ?>
				     </select> 
				</div>
			</div>

			<!--<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Registration</label>
				</div>
				<div class="field col-md-3">
	                <input type='number' class="form-control" name="regfee" id="regfee" value="" />
				</div>

				<div class="field-label col-md-3">
					<label>Transporation</label>
				</div>
				<div class="field col-md-3">
	                <input type='number' class="form-control" name="transfee" id="transfee" value="" />
				</div>
			</div>

			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Access</label>
				</div>
				<div class="field col-md-3">
	                <input type='number' class="form-control" name="accfee" id="accfee" value="" />
				</div>

				<div class="field-label col-md-3">
					<label>Total</label>
				</div>
				<div class="field col-md-3">
	                <input type='number' class="form-control" name="total" id="total" value="" readonly="" />
				</div>
			</div>

			<div class="form-row col-md-12">
				<div class="field-label col-md-3">
					<label>Deadline Date</label>
				</div>
				<div class="field col-md-9">
					<div class='input-group date' id="regdeadline" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd" style="width: 60%;">
	                    <input type='text' class="form-control" size="16" name="regdeadline" id="dfrom" type="text" value="<?=$datetoday?>"/>
	                    <span class="input-group-addon">
	                    	<span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
				</div>
			</div>-->

			<div id="msg_header" style="display: none;">
                <strong></strong><span></span>
            </div>
		</form>
	</div>
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<input type="hidden" id="username" value="<?= $this->session->userdata('username') ?>">
<input type="hidden" id="workshopid" value="<?= (isset($existingData[0]['workshop'])) ? $existingData[0]['workshop'] : '' ?>">
<script>
	$(document).ready(function(){
		if($("#employees").val()){
	      if(!$("#employees").val().includes("all")){
	        $('#employees option[value="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#employees option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	      }else{
	        $('#employees option[value!="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#employees option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      }
	    }else{
	      $('#employees option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      $('#employees option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	    }

	    if($("#attendeesDept").val()){
	      if(!$("#attendeesDept").val().includes("all")){
	        $('#attendeesDept option[value="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#attendeesDept option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	      }else{
	        $('#attendeesDept option[value!="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#attendeesDept option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      }
	    }else{
	      $('#attendeesDept option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      $('#attendeesDept option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	    }

	    if($("#attendeesOffice").val()){
	      if(!$("#attendeesOffice").val().includes("all")){
	        $('#attendeesOffice option[value="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#attendeesOffice option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	      }else{
	        $('#attendeesOffice option[value!="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#attendeesOffice option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      }
	    }else{
	      $('#attendeesOffice option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      $('#attendeesOffice option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	    }
	})

	  $("#employees").change(function(){
	    if($(this).val()){
	      if(!$(this).val().includes("all")){
	        $('#employees option[value="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#employees option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	      }else{
	        $('#employees option[value!="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#employees option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      }
	    }else{
	      $('#employees option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      $('#employees option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	    }
	  });

	  $("#attendeesDept").change(function(){
	    if($(this).val()){
	      if(!$(this).val().includes("all")){
	        $('#attendeesDept option[value="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#attendeesDept option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	      }else{
	        $('#attendeesDept option[value!="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#attendeesDept option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      }
	    }else{
	      $('#attendeesDept option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      $('#attendeesDept option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	    }
	  });

	  $("#attendeesOffice").change(function(){
	    if($(this).val()){
	      if(!$(this).val().includes("all")){
	        $('#attendeesOffice option[value="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#attendeesOffice option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	      }else{
	        $('#attendeesOffice option[value!="all"]').attr("disabled", true).trigger("chosen:updated");
	        $('#attendeesOffice option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      }
	    }else{
	      $('#attendeesOffice option[value="all"]').attr("disabled", false).trigger("chosen:updated");
	      $('#attendeesOffice option[value!="all"]').attr("disabled", false).trigger("chosen:updated");
	    }
	  });

	$("#attendeesDept").change(function(){
	    $.ajax({
	        url : "<?=site_url('setup_/getOfficeMultiple')?>",
	        type: "POST",
	        data: {toks:toks,department:GibberishAES.enc($(this).val(), toks)},
	        success: function(msg){
	            $("#attendeesOffice").html(msg).trigger("chosen:updated");
	        }
	    });
	});




	loadWorkshop();
	callYearEmployees(true);
    $("#transfee, #regfee, #accfee").keypress(function(){
        var trans = $("#transfee").val();
        var reg = $("#regfee").val();
        var acc = $("#accfee").val();
        var total = reg + trans + acc;
        $("#total").val(total);
    });
    var table_ID = $("#button_save_modal").attr("tbl_id");
    if(table_ID){
    	$("#usernameDiv").css("display", "none");
    }
    
    $('.time').datetimepicker({
       	format: 'LT'
    });

    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });

    $("#category").change(function(){
    	loadWorkshop();
    });


    function loadWorkshop(){
    	var category = $("#category").val();
    	var workshopid = $("#workshopid").val();
    	if(category){
    		$.ajax({
	    		url: "<?=site_url('employeemod_/loadWorkshop')?>",
	    		type: 'POST',
	    		data: {category:category, workshopid:workshopid},
	    		success:function(res){
	    			$("#workshop").html(res).trigger("chosen:updated");
	    		}
	    	})
    	}
    }
    

    // function callYearEmployees(onload=false){
    // 	var employees = '';
    // 	if(onload) employees = $("#savedemployees").val();
    // 	$.ajax({
	   //      type : "POST",
	   //      url:  "<?=site_url('seminar_/loadAttendees')?>",
	   //      data: {year_attendees: GibberishAES.enc($("#attendees").val(), toks), employees: GibberishAES.enc(employees, toks),status: GibberishAES.enc('1', toks), toks:toks},
	   //      success: function(data){
	   //          $("#employees").html(data).trigger("chosen:updated");
	   //      }
	   //  });
    // }

    function callYearEmployees(onload=false){
    	var employees = '';
    	if(onload) employees = $("#savedemployees").val();
    	$.ajax({
	        url : "<?=site_url('seminar_/loadAttendeesNew')?>",
	        type: "POST",
	        data: {toks:toks,department:GibberishAES.enc($('#attendeesDept').val(), toks),office:GibberishAES.enc($('#attendeesOffice').val(), toks), employees: GibberishAES.enc(employees, toks)},
	        success: function(msg){
	            $("#employees").html(msg).trigger("chosen:updated");
	        }
	    });
    }


 //    $("#attendees").on("change", function(){
		// callYearEmployees();
	// });

	$("#attendeesOffice, #attendeesDept").change(function(){
	    callYearEmployees();
	});

	$(".chzn-select").chosen();
</script>