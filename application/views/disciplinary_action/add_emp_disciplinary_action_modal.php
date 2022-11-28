<?php
	//Added (6-2-2017)
    $CI =& get_instance();
    $CI->load->model('disciplinary_action');
	$datetoday = "";
    $offense_types = $CI->disciplinary_action->getOffensesTypes();
    $sanction = $CI->disciplinary_action->getSanctions();
	$employee = array();
	$offense = $sancType = $month = $year = $toks = "";
	if($this->input->post("toks")) $toks = $this->input->post("toks");
	if($this->input->post("offense")) $offense = $toks ? $this->gibberish->decrypt( $this->input->post("offense"), $toks ) : $this->input->post("offense");
	if($this->input->post("emplist")) $employee =  $toks ? $this->gibberish->decrypt( $this->input->post("emplist"), $toks ) :$this->input->post("emplist");
	if($this->input->post("sancType")) $sancType =  $toks ? $this->gibberish->decrypt( $this->input->post("sancType"), $toks ) :$this->input->post("sancType");
	if($this->input->post("month")) $month = $toks ? $this->gibberish->decrypt( $this->input->post("month"), $toks ) : $this->input->post("month");
	if($this->input->post("year")) $year = $toks ? $this->gibberish->decrypt( $this->input->post("year"), $toks ) : $this->input->post("year");
	if($this->input->post("emplist")) $employee = explode(",", $employee);
?>

<style type="text/css">
	.form_row{
		padding-bottom: 10px;
	}

	#formOffense{
		margin-top: 30px;
	}
</style>

<form id="formOffense">
	<div>
	<div class="form_row">
		<label class="field_name align_right">Employee</label>
		<div class="field" id='employeeDiv'>
			<div class="col-md-12">
				<select class="form-control chosen isrequired" id="selEmp" size="16" name="employeeid[]" multiple="">
					<option value="allemployee">Select All Employee</option>
					<?
						$opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")),"","",false,'');
						foreach($opt_type as $val){

						?><option value="<?=$val['employeeid']?>" <?=(in_array($val['employeeid'],$employee))?"selected":""?>><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
						}
					?>		
				</select>
				<span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
			</div>
		</div>
	</div>
	<?php if($month && $year){ ?>
		<div class="form_row">
			<label class="field_name align_right"><b>Date of Warning</b></label>
			<div class="field">
				<div class="col-md-12">
		         	<div class='input-group date'id="dateWarning" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
			            <input class="form-control isrequired" size="16" name="dateWarning" id="dateWarning2" type="text" value="<?=$datetoday?>"/>
			            <span class="input-group-addon">
			                <span class="glyphicon glyphicon-calendar"></span>
			            </span>
			        </div>
	          	</div>
			</div>
		</div>
	<?php }else{ ?>
		<div class="form_row">
			<label class="field_name align_right"><b>Date of Warning</b></label>
			<div class="field">
				<div class="col-md-4" style="padding-right: 0px;">
		         	<div class='input-group date'id="dateWarning" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
			            <input class="form-control isrequired" size="16" name="dateWarning" id="dateWarning2" type="text" value="<?=$datetoday?>"/>
			            <span class="input-group-addon">
			                <span class="glyphicon glyphicon-calendar"></span>
			            </span>
			        </div>
	          	</div>
	          	<div class="col-md-4">
		         	<label class="align_right" style="padding-top: 5px;"><b>&emsp;&emsp;&emsp;Date of Violation</b></label>
	          	</div>
	          	<div class="col-md-4"  <?= ($month && $year) ? "style='pointer-events:none; padding-left: 0px;'" : "style='padding-left: 0px;'" ;?>>
					<?php if(!$month && !$year): ?>
				        <div class='input-group date' id="dateViolation" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
				            <input class="form-control isrequired" size="16" name="dateViolation" id="dateViolation2" type="text" value="<?=$datetoday?>"/>
				            <span class="input-group-addon">
				                  <span class="glyphicon glyphicon-calendar"></span>
				            </span>
				        </div>
				    <?php endif;
				    if($month && $year): ?>
				    	<div class="col-md-6">
					    	<select class="chosen  span4" id="year" name="year">
			                	<option value="<?= $year ?>"><?=$year?></option>
			                </select>
				    	</div>
				    	<div class="col-md-6">
				    		<select class="chosen  span4" id="month" name="month">
								<?php foreach($this->extensions->monthSelection() as $key => $value): ?>
			                		<option value="<?= $key ?>" <?= ($key==$month) ? 'selected' : '' ?>><?=$value?></option>
			                	<?php endforeach; ?>
			                </select>
				    	</div>
				    <?php endif; ?>
			    </div>
			</div>
		</div>
	<?php } ?>
    <div class="form_row">
		<label class="field_name align_right"><b>Type of Offense</b></label>
		<div class="field" id='offenseDiv'>
			<div class="col-md-12" <?= ($sancType != "") ? "style='pointer-events:none;'" : "" ;?>>
			    <select class="chosen span5" id="offense" name="offense" >
			    	<option value="" >Select</option>
    		    <?  if($sancType == ""){
	    		        foreach ($offense_types as $row) {?>
	    		            <option value="<?=$row->code?>" <?=(strtoupper($offense) == $row->code)?"selected":"";?>><?=$row->description?></option>
	    		        <?}
    		    	}else{
    		    		foreach ($offense_types as $row) {?>
	    		            <option value="<?=$row->code?>" <?=($row->code == $sancType)?"selected":"";?>><?=$row->description?></option>
	    		        <?}
    		    	}
    		    ?>
    		    </select>
			</div>
		</div>
	</div>
	<?php if($month && $year): ?>
		<div class="form_row">
			<label class="field_name align_right"><b>Date of Violation</b></label>
			<div class="field">
				<div class="col-md-12"  <?= ($month && $year) ? "style='pointer-events:none;'" : "" ;?>>
					<?php if(!$month && !$year): ?>
				        <div class='input-group date' id="dateViolation" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
				            <input class="form-control isrequired" size="16" name="dateViolation" id="dateViolation2" type="text" value="<?=$datetoday?>"/>
				            <span class="input-group-addon">
				                  <span class="glyphicon glyphicon-calendar"></span>
				            </span>
				        </div>
				    <?php endif;
				    if($month && $year): ?>
				    	<div class="col-md-6" style="padding-left: 0px;">
					    	<select class="chosen  span4" id="year" name="year">
			                	<option value="<?= $year ?>"><?=$year?></option>
			                </select>
				    	</div>
				    	<div class="col-md-6" style="padding-right: 0px;">
				    		<select class="chosen  span4" id="month" name="month">
								<?php foreach($this->extensions->monthSelection() as $key => $value): ?>
			                		<option value="<?= $key ?>" <?= ($key==$month) ? 'selected' : '' ?>><?=$value?></option>
			                	<?php endforeach; ?>
			                </select>
				    	</div>
				    <?php endif; ?>
			    </div>
			</div>
		</div>
	<?php endif; ?>
	<div class="form_row">
        <label class="field_name align_right"><b>Employer's Statement</b></label>
        <div class="field no-search">
        	<div class="col-md-12">
            	<textarea rows="4" class="form-control isreq isrequired" name="employeersStatement" id="employeersStatement" placeholder=""></textarea>
            	<span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
            </div>
        </div>
    </div>
	<div class="form_row">
        <label class="field_name align_right"><b>Employee Statement</b></label>
        <div class="field no-search">
        	<div class="col-md-12">
            	<textarea rows="4" class="form-control isreq isrequired" name="empStatement" id="empStatement" placeholder=""></textarea>
            	<span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
            </div>
        </div>
    </div>
    <div class="form_row">
		<label class="field_name align_right"><b>Given Action</b></label>
		<div class="field" id='sanctionDiv'>
			<div class="col-md-12">
			    <select class="chosen col-md-5 isrequired" id="sanction" name="sanction" style="">
			    	<option value="">Select</option>
			        <?
			            foreach ($sanction as $row) {?>
			                <option value="<?=$row->code?>"><?=$row->description?></option>
			        <?}?>
			   </select>
		  	</div>
		</div>
	</div>
</div>
</form>     

<script>
  $("#selEmp").change(function(){
    if($(this).val()){
      if(!$(this).val().includes("allemployee")){
        $('#selEmp option[value="allemployee"]').attr("disabled", true).trigger("chosen:updated");
        $('#selEmp option[value!="allemployee"]').attr("disabled", false).trigger("chosen:updated");
      }else{
        $('#selEmp option[value!="allemployee"]').attr("disabled", true).trigger("chosen:updated");
        $('#selEmp option[value="allemployee"]').attr("disabled", false).trigger("chosen:updated");
      }
    }else{
      $('#selEmp option[value="allemployee"]').attr("disabled", false).trigger("chosen:updated");
      $('#selEmp option[value!="allemployee"]').attr("disabled", false).trigger("chosen:updated");
    }
  });
$("#button_save_modal").unbind('click').bind('click',function(){
	var cancontinue = true;
	var selEmp = $("#selEmp").val();
	if(selEmp === null){
		$("#selEmp_chosen").css("border", "1px solid red").focus();
		cancontinue = false;
	}
	if(cancontinue == true){
		var iscontinue  = validateForm($("#formOffense"));
	 //    $("#formOffense .isreq").each(function(){
		//     if($(this).val() == ""){
		//         $(this).css("border-color","red").attr("placeholder", "This field is required!.");  
		//         iscontinue = false;
		//     }
		// 	else
		// 	{
		// 		 $(this).css("border-color","");  
		// 	}
	 //    });
		
		// $( "#offenseAlert" ).remove();
		// if($('#offense').val() == '')
		// {	
		// 	$( "<label id='offenseAlert' style='color:red;margin-left:23%'><b>This field is required!.</b></label>" ).insertAfter( "#offenseDiv" );
		// 	iscontinue = false;
		// }
		
		// $( "#sanctionAlert" ).remove();
		// if($('#sanction').val() == '')
		// {
		// 	$( "<label id='sanctionAlert' style='color:red;margin-left:23%'><b>This field is required!.</b></label>" ).insertAfter( "#sanctionDiv" );
		// 	iscontinue = false;
		// }
		
		// $( "#employeeAlert" ).remove();
		// if($("select[name='employeeid[]'] option:selected").length == 0)
		// {
		// 	$( "<label id='employeeAlert' style='color:red;margin-left:23%'><b>This field is required!.</b></label>" ).insertAfter( "#employeeDiv" );
		// 	iscontinue = false;
		// }

	    if(iscontinue){
		    $("#saving").hide();
		    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");

		    var formdata   =  '';
		    $('#formOffense input, #formOffense select, #formOffense textarea').each(function(){
		      if(formdata) formdata += '&'+$(this).attr('name')+'='+$(this).val();
		      else formdata = $(this).attr('name')+'='+$(this).val();
		   })
		     $.ajax({
		       url      :   "<?=site_url("disciplinary_action_/batchSaveEmployeeOffense")?>",
		       type     :   "POST",
	           dataType :   "html",
		       data     :   {form_data: GibberishAES.enc( formdata, toks), toks:toks},
		       success  :   function(msg){
		        if(msg == "Success."){
		        	Swal.fire({
					      icon: 'success',
					      title: 'Success!',
					      text: "Employee Offense has been saved successfully.",
					      showConfirmButton: true,
					      timer: 2000
					  })
		        }else{
		        	Swal.fire({
			          icon: 'warning',
			          title: 'Warning!',
			          text: msg,
			          showConfirmButton: true,
			          timer: 2000
			      })
		        }
				$('#modal-view').modal('hide');
				<?if($offense){?>
					$("a[name='reload'").click();
				<?}?>
		        
		       },
	           error : function(XMLHttpRequest, textStatus, errorThrown) { 
	                console.log("Status: " + textStatus); console.log("Error: " + errorThrown); 
	            }      
		    });

		}
	}
    
});

$("#event, #venue").on('input',function(){
	$(this).css("border-color","#AAAAAA").attr("placeholder", "");
});

$(".date").datetimepicker({
	 format: "YYYY-MM-DD"
});

$(".chosen").chosen();
</script>