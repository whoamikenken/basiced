<?
	// $employeeid = $this->input->post('employeeid');
	// $name = $this->input->post('name');
	// $management = $this->input->post('management');
	// $deptid = $this->input->post('deptid');
 //  $officeid = $this->input->post('officeid');
	// $employmentstat = $this->input->post('employmentstat');
	// $position = $this->input->post('position');
 //  $dateres = $this->input->post('dateres');
	// $datepos = $this->input->post('datepos');
	// $duration = $this->input->post('duration');
 //  $reason = $this->input->post('reason');
	
	$date_mature = strtotime(date("Y-m-d", strtotime($datepos)) . "+{$duration} months");
  $date_mature = date("Y-m-d",$date_mature);
?>

<form id="info_form">
<input type="hidden" name="dateresigned" value="<?=$dateres?>">
<input type="hidden" name="datepos" value="<?=$date_mature?>">
<input type="hidden" name="reason" value="<?=$reason?>">
<h5 style="font-weight:bold;margin-left: 20px;">Employee:&nbsp;&nbsp;&nbsp;&nbsp; <?=$name?></h5>
  <br>
	<input type="hidden" name="employeeid" value="<?=$employeeid?>" />
    <div class="form_row">
      <label class="field_name align_right">Department</label>
      <div class="col-md-9">
          <select class="chosen" name="deptid" id="deptid">
          <?
            $opt_department = $this->extras->showdepartment();
            foreach($opt_department as $c=>$val){
            ?><option<?=($c==$deptid ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
            }
          ?>
          </select>
      </div>
    </div><br>
    <div class="form_row">
      <label class="field_name align_right">Office</label>
      <div class="col-md-9">
          <select class="chosen" name="office" id="office">
          <?
            $opt_office = $this->extras->showoffice();
            foreach($opt_office as $c=>$val){
            ?><option<?=($c==$officeid ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
            }
          ?>
          </select>
      </div>
    </div><br>
	<div class="form_row" hidden>
      <label class="field_name align_right">Management Level</label>
      <div class="col-md-9">
          <select class="chosen" name="managementid" id="managementid" disabled>
          <?
            $opt_type = $this->extras->showManagement();
            foreach($opt_type as $c=>$val){
            ?><option<?=($c==$management ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
            }
          ?>
          </select>
      </div>
    </div>
    <div class="form_row">
      <label class="field_name align_right">Employee Status</label>
      <div class="col-md-9">
          <select class="chosen" name="employmentstat">
          <?
            $opt_status = $this->extras->showemployeestatus();
            foreach($opt_status as $c=>$val){
            ?><option<?=($c==$employmentstat ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
            }
          ?>
          </select>
      </div>
    </div>
    <div class="form_row">
      <label class="field_name align_right">Position</label>
      <div class="col-md-9">
          <select class="chosen" name="positionid">
          <?
            $opt_type = $this->extras->showPostion();
            foreach($opt_type as $c=>$val){
            ?><option<?=($c==$position ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
            }
          ?>
          </select>
      </div>
    </div><br>
    <div class="form_row">
      <label class="field_name align_right">Start Date</label>
      <div class="col-md-9">
        <div class='input-group date datepos' data-date="<?=date('Y-m-d');?>" data-date-format="yyyy-mm-dd">
          <input type='text' class="form-control" id="dateposition" value="<?=$date_mature?>" />
          <span class="input-group-addon">
                <span class="glyphicon glyphicon-calendar"></span>
          </span>
        </div>
      </div>
    </div>


    <div class="field">
        <span id="errmsg"></span>
    </div>
</div>
</form>

<script>
  var toks = hex_sha512(" ");
   $("#button_save_modal").unbind("click").click(function(){
	   var date_mature = new Date("<?=$date_mature?>");
	   var dateposition = new Date($("#dateposition").val());
	   $('#managementid').prop('disabled', false);
		// if(date_mature < dateposition)
		// {
			$("#errmsg").html("<h6>This may take a while, please wait...</h6>");
			var form_data = $('#info_form').serialize();
			$('#managementid').prop('disabled', true);
			

			$('#info_form input').attr("disabled",true);
			if($("#info_form").valid()){
				 $.ajax({
					url:"<?=site_url("employee_/editEStat")?>",
					type:"POST",
					data:form_data,
					dataType: 'JSON',
					success: function(msg){
            if(msg.err_code == 0){
              Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: msg.msg,
                  showConfirmButton: true,
                  timer: 1000
              })
              $('#modalclose').click();
              document.location.reload();
            }else{
              Swal.fire({
                  icon: 'warning',
                  title: 'Warning!',
                  text: msg.msg,
                  showConfirmButton: true,
                  timer: 1000
              })

              return false;
            }
            }
				 });
		   }else {
			   $validator.focusInvalid();
			   return false;
		   }
		   // alert("Success");
		// }
		// else
		// {
		// 	alert("Error: Conflict on date");
		// }
    });

   $(".chosen").chosen();

   $('.datepos').datetimepicker({
    format: 'YYYY-DD-MM'
    });
   
   //Addedd 5-29-17
   $( "#deptid" ).change(function() {
		deptid = $( "#deptid" ).val();
		$.ajax({
			url:"<?=site_url("employee_/getManagementLevel")?>",
            type:"POST",
			data:{deptid : deptid},
			success: function(management){
				$("#managementid").val(management);
			}
		});
	});

   $("#deptid").change(function(){
    $.ajax({
        url:"<?=site_url("setup_/getOffice")?>",
        type: "POST",
        data: {department:$(this).val()},
        success: function(msg){
            $("#office").html(msg).trigger("chosen:updated");
        }
    });
});

</script>