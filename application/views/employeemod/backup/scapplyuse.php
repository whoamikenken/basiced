<?php
	$CI = &get_instance();
	$CI->load->model("service_credit");
	$datetoday = date("d-m-Y");
	$employeeid = $this->session->userdata("username");
?>
<style>
.modal{
    width: 50%;
    left: 0;
    right: 0;
    margin: auto;
}
</style>
<form id="frmsc">
<input name="model" value="applySCWithSequence" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="7%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                    <td></td>
                </tr>
                <tr>
                    <td><strong>Service Credit Application</strong></td>
                </tr>

            </table>
        </div>
        <div class="modal-body">
            <div class="content">
				<div class="form_row">
                    <label class="field_name align_right">Date when Service Credit will be use</label>
                    <div class="field no-search">
                         <div class="input-group date" id='datePicker' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="date" id="date" type="text" value="" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
						<span id='message' style='color:red'></span>
                    </div>
                </div>
				<div class="form_row">
                    <label class="field_name align_right">Day Mode</label>
                    <div class="field no-search">
                        <select name='dayMode' id='dayMode' class='chosen'>
							<option value='whole'>Whole Day</option>
							<option value='half'>Half Day</option>
						</select>
                    </div>
                </div>
				<div class="form_row">
                    <label class="field_name align_right">Needed Service Credit</label>
					<div class="field no-search">
						<input type='text' name="nsc" id="nsc" value="" readonly/>
					</div>
                </div>
				
                <div class="form_row">
                    <label class="field_name align_right">Service Credit Date</label>
					<div class="field no-search">
						<table width='70%'>
							<tr>
								<td>
									<select class='chosen' id='scdate' name='scdate'></select>
								</td>
								<td>
									<label class="field_name align_right">Days</label>
									<div class="field no-search">
										<input type='text' name="sc" id="sc" value="" readonly/>
									</div>
								</td>
								<td>
									<label id='date1' style='display:none'></label>
								</td>
							</tr>
							<tr id='second' style='display:none' >
								<td>
									<select class='chosen' id='scdate2' name='scdate2'></select>
								</td>
								<td>
									<label class="field_name align_right">Days</label>
									<div class="field no-search">
										<input type='text' name="sc2" id="sc2" value="" readonly/>
									</div>
								</td>
								<td>
									<label id='date2' style='display:none'></label>
								</td>
							</tr>
							
						</table>
                    </div>
                </div>

                <div class="form_row">
                    <label class="field_name align_right">Remark</label>
                    <div class="field no-search">
                        <textarea rows="4" style="width: 100%;resize: none;" name="remark" id="remark" placeholder="Reason"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span id="errormsg" hidden="" style="color: red; margin-right: 20px;"></span>
            <span id="loading" hidden=""></span>
            <span id="saving">
                <button type="button" id="save" class="btn btn-danger">Save</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </span>
        </div>
    </div>
</div>
</form>
<script>
$(document).ready(function(){
	$("#saving").hide();
});

//FOR DATE VALIDATION
$("#date").change(function(){
	var date = $(this).val();
	$("#saving").hide();
	$("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />");
	$("#message").hide()

    $.ajax({
		url      :   "<?=site_url("service_credit_/checkDateAvailability")?>",
		type     :   "POST",
		data     :   {date:date, empid:"<?=$employeeid?>"},
		dataType : 	'json',
		success  :   function(result){
			if(result.exist)
			{
				if(result.count > 1)
				{
					$("#dayMode").val('whole').change().trigger("liszt:updated");
				}
				else
				{
					$("#dayMode").val('half').change().trigger("liszt:updated");
				}
				
				$("#saving").show();
			}
			else
			{
				$("#message").show().html("You don`t have schedule on that day!");
			}
			
		
			$("#loading").hide()
		}
    });
});

$("#dayMode").change(function(){
	var dayMode = $(this).val();
	if(dayMode == 'whole')
	{
		$("#nsc").val(1);
	}
	else
	{
		$("#nsc").val(0.5);
	}
	updateServiceCreditDateList('scdate','sc');
	$("#second").hide();
});

$("#scdate").change(function(){
	$("#second").hide();
	var available_sc = $(this).find(":selected").attr('available_sc');
	if($('#nsc').val() >= available_sc)
	{
		$('#sc').val(available_sc);
	}
	else
	{
		$('#sc').val(available_sc - $('#nsc').val());
	}

	if($('#nsc').val() - $('#sc').val() != 0)
	{
		$("#second").show();
		updateServiceCreditDateList('scdate2','sc2');
	}
});

$("#scdate2").change(function(){
	var available_sc = $(this).find(":selected").attr('available_sc');
	if(($('#nsc').val() - $('#sc').val()) >= available_sc)
	{
		$('#sc2').val(available_sc);
	}
	else
	{
		$('#sc2').val(available_sc - ($('#nsc').val() - $('#sc').val()));
	}
});

$("#save").unbind("click").bind("click",function(){
	var iscontinue = true;
	$("#message").hide()
	$("#date1").hide()
	$("#date2").hide()
	$("#errormsg").hide();
	if($("#date").val() == "")
	{
		$("#message").show().html("This is required!");
		iscontinue = false;
	}
	
	if($("#scdate").val() == "")
	{
		$("#errormsg").html("Must achieve needed service credit!").show();
		iscontinue = false;
	}
	else
	{
		if($("#nsc").val() > $("#sc").val())
		{
			if($("#scdate2").val() == "")
			{
				$("#errormsg").html("Must achieve needed service credit!").show();
				iscontinue = false;
			}
		}
	}
	
	if($("#remark").val() == "")
	{
		$("#remark").css("border-color","red").attr("placeholder", "This field is required!.").focus();
		iscontinue = false;
	}
	else
	{
		$("#remark").css("border-color","");
	}



	if(iscontinue)
	{
		var form_data   =   $("#frmsc").serialize();
		$("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("service_credit_/saveSCAppUse")?>",
           type     :   "POST",
           data     :   form_data,
           success  :   function(msg){
            $(function(){
              $("#close").click();
            });
            alert(msg);
            loadscuhistory();
           }
        });
	}
	
});

$(".chosen").chosen();
$("#datePicker").datepicker();

function updateServiceCreditDateList(dateField,dayField)
{
	$("#"+dateField).html("").trigger("liszt:updated");	
	$("#"+dayField).val("");	
		
	$.ajax({
		url      :   "<?=site_url("service_credit_/getSCDatesWithAvailable")?>",
		type     :   "POST",
		dataType : 	'html',
		success  :   function(result){
			$("#"+dateField).html(result).trigger("liszt:updated");	
			if(dateField == "scdate2")
			{
				var first = $("#scdate :selected").val();
				$("#"+dateField+" option[value='"+first+"']").remove();
			}
			$("#"+dateField).trigger("liszt:updated");	
		}
	});
}
</script>