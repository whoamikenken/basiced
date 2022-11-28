<?
/**
* @author justin (with e)
* @copyright 2018
*/
$CI =& get_instance();
$CI->load->model('utils');
?>

<style type="text/css">
	.num_only{
		text-align: right;
		width: 100px;
	}

	.percentInput{
		position:relative;
		display: inline-block;
	}
    .percentInput span{
    	position: absolute;
    	top:2px;
    	right: 4px;
    	line-height:100%;
    	vertical-align: middle;
    	padding-top: 8px;
    	font-weight: 900;
    }
    .num_only{
 		text-align: right;
 		padding-right: 15px;
    }
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}

.swal2-cancel{
    margin-right: 20px;
}

</style>
<input type="hidden" id="saveStatus" value="Overtime Rate has been saved successfully.">
<div class="modal fade" id="myModal" data-backdrop="static"></div>
<a href="#" data-toggle="modal" id="show_modal" data-target="#myModal" hidden></a>
<div id="content"> 
  	<div class="widgets_area">
  		<div class="row">
  			<div class="col-md-12">
				<div class="panel animated fadeIn delay-1s">
				   <div class="panel-heading" style="background-color: #0072c6;"><h4><strong>Overtime Setup</strong></h4></div>
				   <div class="panel-body" style="margin-top: 15px; margin-bottom: 15px;">
		            	<form id="overtime_frm">
		            	<input type="hidden" name="ot_id" value="new">
		            	<div class="form_row">
		            		<label class="field_name align_right" style="margin-left: -106px;"><strong>Employee Status:</strong></label>
		            		<div class="field">
		            			<div class="col-md-9"  style="margin-left: -139px; width: 98%;">
		            				<select class="chosen col-md-6" id="status" name="status" multiple>
		            					<option value="all">All Status</option>
		            				<?
		            					$q_code_status = $CI->utils->getCodeStatus();
		            					foreach ($q_code_status as $row) {
		            				?>
		            					<option value="<?=$row->code?>"><?=$row->description?></option>
		            				<?
		            					}
		            				?>
		            				</select>
		            			</div>
		            		</div>
		            	</div>

		            	<div class="form_row">
		            		<table width="100%">
		            			<thead>
		            				<tr>
		            					<th colspan="2" width="20%"><strong>&nbsp;</strong></th>
		            					<th width="16%">&nbsp;</th>
		            					<th colspan="2" width="33.33%"><strong  style="margin-left: 60px;">Regular Holiday</strong></th>
		            					<th colspan="2" width="33.33%"><strong style="margin-left: -60px;">Special Holiday / Other</strong></th>
		            				</tr>
		            			</thead>
		            			<tbody>
		            			<?
		            				$ot_types = $CI->utils->getOvertimeTypes();
		            				foreach ($ot_types as $key => $caption) {
		            			?>
		            				<tr id="<?=$key?>_tr">
		            					<td class="align_right">
		            							<label style="margin-right: 15px;margin-bottom: 37px;"><strong><?=$caption?> </strong></label> 
		            					</td>
		            					<td class="align_center percentInput">
		            							<input type="text" class="num_only form form-control" name="percent" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" >
		            							<span >%</span>
		            					</td>
		            					<td class="align_center">
		            							<label style="margin-left: -100px ;margin-bottom: 30px;"><strong style="margin-right: 35px;">Excess </strong></label> 
		            					</td>
		            					<td class="align_center percentInput">
		            							<input type="text" class="num_only form form-control" name="excess" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');" style="margin-left: -140px">
		            							<span style="margin-right: 41px;">%</span>
		            					</td>
		            					<td class="align_center percentInput" style="margin-left: 66px;">
		            							<input type="text" class="num_only form form-control" name="regular_percent" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
		            							<span style="margin-right: 0px;">%</span>
		            					</td>
		            					<td class="align_center">
		            							<label style="margin-left: -580px; margin-bottom: 30px;"><strong style="margin-right: 35px;">Excess </strong></label> 
		            					</td>
		            					<td class="align_center percentInput" style="margin-left: -227px">
		            							<input type="text" class="num_only form form-control" name="regular_excess" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
		            							<span style="margin-right: 0px;">%</span>
		            					</td>
		            					<td class="align_center percentInput" style="margin-left: 100px;">
		            							<input type="text" class="num_only form form-control" name="other_percent" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
		            							<span style="margin-right: 0px;">%</span>
		            					</td>
		            					<td class="align_center">
		            							<label style="margin-left: -690px; margin-bottom: 30px;"><strong style="margin-right: 35px;">Excess </strong></label> 
		            					</td>
		            					<td class="align_center percentInput" style="margin-left: -280px;">
		            							<input type="text" class="num_only form form-control" name="other_excess" oninput="this.value = this.value.replace(/[^0-9.]/g, '').replace(/(\..*)\./g, '$1');">
		            							<span style="margin-right: 0px;">%</span>
		            					</td>
		            				</tr>
		            			<?		
		            				}
		            			?>
		            			</tbody>
		            		</table>
		            	</div>
		            	<br>
		            	<div class="form_row">
		            		<div class="field_name align_right">
		            			<button type="button" id="cancel" class="btn btn-danger">Cancel</button>
			            		<button type="button" id="save" class="btn btn-success"> Save </button>
			            		
		            		</div>
		            	</div>
		            		
		            	</form>
		            </div>
  				</div>
  			</div>
  		</div>

  		<div class="row">
  			<div class="col-md-12">
  				<div class="panel animated fadeIn delay-1s">
				   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Overtime Setup List</b></h4></div>
				   <div class="panel-body">
		            <div class="well-content" id="overtime_history_setup">
		            	<!-- displayed here history -->
		            </div>
  				</div>
  			</div>
  		</div>
  	</div>
</div> 

<script type="text/javascript">
var toks = hex_sha512(" ");
var ot_types = {
	"WITH_SCHED" : "With Sched", 
	"WITH_SCHED_WEEKEND" : "Week End w/ Sched", 
	"NO_SCHED" : "No Sched", 
	"NIGHT_DIFF" : "Night Diff"
};


$(document).ready(function(){
	$('.num_only').keypress(function(event) {
	  	var position = this.selectionStart - 1;
	  	
		//remove all but number and .
		var fixed = this.value.replace(/[^0-9\.]/g, '');
		if (fixed.charAt(0) === '.')                  //can't start with .
		fixed = fixed.slice(1);

		var pos = fixed.indexOf(".") + 1;
		if (pos >= 0)               //avoid more than one .
		fixed = fixed.substr(0, pos) + fixed.slice(pos).replace('.', '');

		if (this.value !== fixed) {
			this.value = fixed;
			this.selectionStart = position;
			this.selectionEnd = position;
		}  
	});

	loadOvertimeSetupList();
	$(".chosen").chosen();
});

	$("#status").on("change", function(){
		var statuslist = $(this).val();
		if(statuslist !== null){
			$.each( statuslist, function( key, value ) {
				if(value == "all"){
					$("#status_chosen .chosen-drop").css("pointer-events", "none");
				}
				else{
					if(statuslist != null){
					var itemToDisable = $("option:contains('All Status')");
					itemToDisable.css("pointer-events", "none");
					$("#status").trigger("chosen:updated");
					}
					else{
					
					}
					// $("#employeeid option[value='']").attr("disabled", "disabled");
				}
			});
		}else{
			$('#status').trigger("chosen:updated"); 
			$(".chosen-drop").css("pointer-events", "");
			var itemToEnable = $("option:contains('All Status')");
			itemToEnable.css("pointer-events", "");
			$("#status").trigger("chosen:updated");
		}

	});

$("#cancel").unbind('click').click(function(){
	clearAll();
});

function clearAll(){
	$("input[name='ot_id']").val('new');
	$("select[name='status']").removeAttr('disabled');
	$("select[name='status']").val('').trigger("chosen:updated");
	$("#saveStatus").val("Overtime Rate has been saved successfully.");
	for(key in ot_types){
		var t_row = $("#"+ key +"_tr");

		t_row.find("input[name='percent']").val('');
		t_row.find("input[name='excess']").val('');
		t_row.find("input[name='regular_percent']").val('');
		t_row.find("input[name='regular_excess']").val('');
		t_row.find("input[name='other_percent']").val('');
		t_row.find("input[name='other_excess']").val('');
	}
}

function validateOTSetupForm(){
	var is_continue = true;
	var error_msg = "";

	if(!$("#status").val()) is_continue = false;

	if(is_continue){
		for(key in ot_types){
			var t_row = $("#"+ key +"_tr");

			if(!t_row.find("input[name='percent']").val()) is_continue = false;
			if(!t_row.find("input[name='excess']").val()) is_continue = false;
			if(!t_row.find("input[name='regular_percent']").val()) is_continue = false;
			if(!t_row.find("input[name='regular_excess']").val()) is_continue = false;
			if(!t_row.find("input[name='other_percent']").val()) is_continue = false;
			if(!t_row.find("input[name='other_excess']").val()) is_continue = false;
		}
	}

	return is_continue;
}

$("#save").unbind('click').click(function(){
	var formdata = {};
	var is_continue = validateOTSetupForm();
	var saveStatus = $("#saveStatus").val();
	if(!is_continue){
		Swal.fire({
	          icon: 'warning',
	          title: 'Warning!',
	          text: 'Please complete to setup the overtime rate.',
	          showConfirmButton: true,
	          timer: 1000
	      })
		return;
	}

	formdata['id'] = GibberishAES.enc($("input[name='ot_id']").val(), toks);
	formdata['toks'] = toks;
	formdata['status'] = GibberishAES.enc($("select[name='status']").val(), toks);
	formdata['ot_types'] = {};
	for(key in ot_types){
		formdata['ot_types'][key] = {};
		var t_row = $("#"+ key +"_tr");

		formdata['ot_types'][key]['percent'] 			= GibberishAES.enc(t_row.find("input[name='percent']").val(), toks);
		formdata['ot_types'][key]['excess'] 			= GibberishAES.enc(t_row.find("input[name='excess']").val(), toks);
		formdata['ot_types'][key]['regular_percent'] 	= GibberishAES.enc(t_row.find("input[name='regular_percent']").val(), toks);
		formdata['ot_types'][key]['regular_excess'] 	= GibberishAES.enc(t_row.find("input[name='regular_excess']").val(), toks);
		formdata['ot_types'][key]['other_percent'] 		= GibberishAES.enc(t_row.find("input[name='other_percent']").val(), toks);
		formdata['ot_types'][key]['other_excess'] 		= GibberishAES.enc(t_row.find("input[name='other_excess']").val(), toks);
	}
	
	$.ajax({
		url : "<?=site_url('overtime_/saveOvertimeSetup')?>",
		type : "POST",
		data : formdata,
		success : function(result){
			$("#show_modal").click();
			Swal.fire({
	              icon: 'success',
	              title: 'Success!',
	              text: saveStatus,
	              showConfirmButton: true,
	              timer: 1000
	         })
			clearAll();
			loadOvertimeSetupList();
		}
	});
});

function loadOvertimeSetupList(){
	$.ajax({
		url : "<?=site_url("overtime_/loadOvertimeSetupList")?>",
		type : "POST",
		data : {},
		success : function(content){
			$("#overtime_history_setup").html(content);
		}	
	});
}

function showOvertimeSetup(code, data){
	$("#saveStatus").val("Overtime Rate has been updated successfully.");
	$("select[name='status']").attr("disabled", "disabled");
	$("select[name='status']").val(code).trigger("chosen:updated");
	for(key in data['ot_types']){
		$("input[name='ot_id']").val('update');
		var t_row = $("#"+ key +"_tr");
		var info = data['ot_types'][key];

		t_row.find("input[name='percent']").val(info['percent']);
		t_row.find("input[name='excess']").val(info['excess']);
		t_row.find("input[name='regular_percent']").val(info['regular_percent']);
		t_row.find("input[name='regular_excess']").val(info['regular_excess']);
		t_row.find("input[name='other_percent']").val(info['other_percent']);
		t_row.find("input[name='other_excess']").val(info['other_excess']);
	}
}

if("<?=$this->session->userdata('canwrite')?>" == 0) $(".btn").css("pointer-events", "none");
else $(".btn").css("pointer-events", "");

</script>