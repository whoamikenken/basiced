<?php 
$opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")));	
?>
<form id="myForm" class="form-horizontal">
	<div class="form-group">
	<label class="col-sm-3 control-label">Status</label>
		<div class="col-sm-7">
			<select class="chosen" name="status" id="status" required>
				<option value="all">All</option>
                <option value="1" selected>Active</option>
                <option value="0">Inactive</option>
		  	</select> 
		</div>
	</div>
	<div class="form-group">
	<label class="col-sm-3 control-label">School Year</label>
		<div class="col-sm-7">
		  <select class="chosen" name="year" id="year" required>
		  	<option value="">- Select - </option>
		  		<?php foreach ($sy as $key => $value): ?>
		  			<option value="<?= $value['sy'] ?>"><?= $value['sy'] ?></option>
		  		<?php endforeach; ?>
		  </select> 
		</div>
	</div>
	<!-- <div class="form-group">
	<label class="col-sm-3 control-label">Month</label>
		<div class="col-sm-7">
		  <select class="chosen" name="month" id="month" required>
		  	<option value="">- Select All - </option>
		  		<?php foreach (Globals::monthList() as $key => $value): ?>
		  			<option value="<?= $key ?>"><?= $value ?></option>
		  		<?php endforeach; ?>
		  </select> 
		</div>
	</div> -->
	<div class="form-group">
	<label class="col-sm-3 control-label">Sort by</label>
		<div class="col-sm-7">
			<select class="chosen" name="sortby" id="sortby" required>
				<option value="Alphabetical">Name</option>
				<option value="Office">Office</option>
		  	</select> 
		</div>
	</div>
	<div class="form-group">
	<label class="col-sm-3 control-label">Year Attendees</label>
		<div class="col-sm-7">
			<select class="form-control chosen attendees" name="attendees" id="attendees">
				<option value="all">- Select All -</option>
                <?= Globals::employmentYearList(); ?>
		    </select> 
		</div>
	</div>
	<div class="form-group">
	<label class="col-sm-3 control-label">Employee</label>
		<div class="col-sm-7">
			<select class="form-control chosen employees" name="employees[]" id="employees" multiple data-placeholder="- Select -">
				<option value="all">Select All Employee</option>
                <?php
                	foreach($opt_type as $val){
                        ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . " " . $val['fname'] . " " . $val['mname'])?></option><?    
                    }
                ?>
		    </select> 
		</div>
	</div>
	<div class="form-group">
	<label class="col-sm-3 control-label">Format</label>
		<div class="col-sm-7">
			<select class="form-control chosen" name="format" id="format">
                <option value="PDF">PDF</option>
                <option value="EXCEL">EXCEL</option>
		    </select> 
		</div>
	</div>
</form>
<script type="text/javascript">
	$(".chosen").chosen();
	var toks = hex_sha512(" ");
	$("#attendees, #status").on("change", function(){
		$.ajax({
	        type : "POST",
	        url:  "<?=site_url('seminar_/loadAttendees')?>",
	        data: {year_attendees: GibberishAES.enc($("#attendees").val(), toks),status: GibberishAES.enc($("#status").val(), toks), toks:toks},
	        success: function(data){
	            $("#employees").html(data).trigger("chosen:updated");
	        }
	    });
	});
	// $("#employees").on("change", function(){
	// 	var employees = $(this).val();
	// 	if(employees !== null){
	// 		$.each( employees, function( key, value ) {
	// 			if(value == "all"){
	// 				$("#employees_chosen .chosen-drop").css("pointer-events", "none");
	// 			}
	// 			else{
	// 				if(employees != null){
	// 				var itemToDisable = $("option:contains('Select All Employee')");
	// 				itemToDisable.css("pointer-events", "none");
	// 				$("#employees").trigger("chosen:updated");
	// 				}
	// 				else{
					
	// 				}
	// 				// $("#employeeid option[value='']").attr("disabled", "disabled");
	// 			}
	// 		});
	// 	}else{
	// 		$('#employees').trigger("chosen:updated"); 
	// 		$(".chosen-drop").css("pointer-events", "");
	// 		var itemToEnable = $("option:contains('Select All Employee')");
	// 		itemToEnable.css("pointer-events", "");
	// 		$("#employees").trigger("chosen:updated");
	// 	}

	// });

	$("#button_save_modal").click(function(){
        var formdata = "";  
        if($("#year").val() == ""){
        	Swal.fire({
			    icon: 'warning',
			    title: 'Warning!',
			    text: 'School year is required.',
			    showConfirmButton: true,
			    timer: 1000
			})
			return false;
        }


		$('#myForm input, #myForm select, #myForm textarea').each(function(){
		    if(formdata) formdata += '&'+$(this).attr('name')+'='+ GibberishAES.enc($(this).val(), toks);
		    else formdata = $(this).attr('name')+'='+ GibberishAES.enc($(this).val(), toks);
		})
		formdata += '&toks='+  toks;
        var report_type = $("#format").val();
        var encodedData = encodeURIComponent(window.btoa(formdata));
        if(report_type == "PDF"){
            window.open("<?=site_url("seminar_/attendedEmployeePFDReport")?>?formdata="+encodedData,"");
        }else{
            window.open("<?=site_url("seminar_/attendedEmployeeEXCELReport")?>?formdata="+encodedData,""); 
        }
    });
</script>