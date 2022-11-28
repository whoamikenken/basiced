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
                    <label class="field_name align_right">Service Credit Date</label>
					<div class="field no-search">
						<table width='100%' id='table'>
							<tr>
								<td>
									<select class='chosen scdate' id='scdate' name='scdate'></select>
								</td>
								<td>
									<label class="field_name align_right">Days</label>
									<div class="field">
										<select class='chosen sc'>
											
										</select>
									</div>
								</td>					
								<td>
									<a class="btn btn-primary" id="add" href="#" class="btn btn-default" style='display:none'><i class='glyphicon glyphicon-plus'></i></a>
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
$(".date").change(function(){
	$("#table tr:gt(1)").detach();
	$("#add").hide();
	updateServiceCreditDateList('scdate','sc');
});

$(".scdate").change(function(){
	var available_sc = $(this).find(":selected").attr('available_sc');
	if(available_sc == 1)
	{
		var data = new Array("1","0.5");
	}
	else
	{
		var data = new Array("0.5","0.5");
	}
	
	var list = $(".sc");
	$.each(data, function(index, a) {
	  list.append(new Option(a.text, a.value));
	});
});

$(".sc").change(function(){
	if($(this).val() < 1)
	{
		$("#add").show()
	}
	else
	{
		$("#add").hide()
	}
});	

var serviceCreditUsed = 1;
$("#add").click(function(){
	if(serviceCreditUsed == 1)
	{
		var dates = $(this).closest("scdate").html()
		var tr = "<tr><td><select class='chosen scdate'></select></td><td><label class='field_name align_right'>Days</label><div class='field'><select name='sc' class='chosen' id='sc'><option>0.5</option></select></div></td></tr>";
		$(this).closest("table").append(tr);
		serviceCreditUsed += 1;
		$(".chosen").chosen();
	}
});

$("#save").unbind("click").bind("click",function(){
	// var iscontinue = true;
	// $("#message").hide()
	// $("#date1").hide()
	// $("#date2").hide()
	// $("#errormsg").hide();
	// if($("#date").val() == "")
	// {
		// $("#message").show().html("This is required!");
		// iscontinue = false;
	// }
	
	// if($("#scdate").val() == "")
	// {
		// $("#errormsg").html("Must achieve needed service credit!").show();
		// iscontinue = false;
	// }
	// else
	// {
		// if($("#nsc").val() > $(".sc").val())
		// {
			// if($("#scdate2").val() == "")
			// {
				// $("#errormsg").html("Must achieve needed service credit!").show();
				// iscontinue = false;
			// }
		// }
	// }
	
	// if($("#remark").val() == "")
	// {
		// $("#remark").css("border-color","red").attr("placeholder", "This field is required!.").focus();
		// iscontinue = false;
	// }
	// else
	// {
		// $("#remark").css("border-color","");
	// }
	
	// if(iscontinue)
	// {
		// var form_data   =   $("#frmsc").serialize();
		// $("#saving").hide();
        // $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
        // $.ajax({
           // url      :   "<?=site_url("service_credit_/saveSCAppUse")?>",
           // type     :   "POST",
           // data     :   form_data,
           // success  :   function(msg){
            // $(function(){
              // $("#close").click();
            // });
            // alert(msg);
            // loadscuhistory();
           // }
        // });
	// }
	
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
			$("."+dateField).html(result).trigger("liszt:updated");	
		}
	});
}
</script>