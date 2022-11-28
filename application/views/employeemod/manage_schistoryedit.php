<?php
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
                    <td><strong>Service Credit Edit History</strong></td>
                </tr>

            </table>
        </div>
        <div class="modal-body">
            <div class="content">
                <div class="form_row">
                    <label class="field_name align_right">Date of Service Credit </label>
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
                    <label class="field_name align_right">Service Credit</label>
                    <div class="field no-search">
                        <input type='text' name="sc" id="sc" value="1" readonly/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason"></textarea>
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
    $("#datePicker").datepicker({
        autoclose: true,
        todayBtn : true
    });
});

$("#save").unbind("click").bind("click",function(){
	$("#errormsg").html('');
	var form_data   =   $("#frmsc").serialize();
	if($("input[name='date']").val() == ""){
        $("#errormsg").show().html("Service Credit Date is required!.");
        return false;
    }else if($("#reason").val() == ""){
        $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
        return false;
    }else{
        $("#saving").hide();
        $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        $.ajax({
           url      :   "<?=site_url("service_credit_/saveSCApp")?>",
           type     :   "POST",
           data     :   form_data,
           success  :   function(msg){
            $(function(){
              loadsc();
              $("#close").click();
            });
            alert(msg);
            loadschistory();
           }
        });
    }
});

//FOR DATE VALIDATION
// $("#date").change(function(){
	// var date = $(this).val();
	// $("#saving").hide();
	// $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />");
	// $("#message").hide()
	// $("#dayMode option[value='whole']").attr('disabled',false).trigger("liszt:updated");
    // $.ajax({
		// url      :   "<?=site_url("service_credit_/checkDateAvailability")?>",
		// type     :   "POST",
		// data     :   {date:date, empid:"<?=$employeeid?>"},
		// dataType : 	'json',
		// success  :   function(result){
			// if(result.availability)
			// {
				// if(result.exist)
				// {
					
					// if(result.count == 1)
					// {
						// $("#dayMode").val('half').change().trigger("liszt:updated");
						// $("#dayMode option[value='whole']").attr('disabled',true).trigger("liszt:updated");
						// $("#saving").show();
						
					// }
					// else if(result.count > 1)
					// {
						// $("#message").html("Warning : You have schedule on this date !").show();
					// }
				// }
				// else
				// {
					// $("#dayMode").val('whole').change().trigger("liszt:updated");
					// $("#saving").show();
				// }
					
			// }
			// else if(!result.availability)
			// {
				// $("#message").html("Warning : This date is already used !").show();
			// }
			// $("#loading").hide()
		// }
    // });
// });

$("#dayMode").change(function(){
	var dayMode = $(this).val();
	if(dayMode == 'whole')
	{
		$("#sc").val(1);
	}
	else
	{
		$("#sc").val(0.5);
	}
});

$(".chosen").chosen();
</script>