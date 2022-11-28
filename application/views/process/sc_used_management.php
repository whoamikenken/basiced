<?php
	$CI = &get_instance();
	$CI->load->model("service_credit");
	$datetoday = date("d-m-Y");
	$employeeid = $this->session->userdata("username");
	$CI->load->model('utils');

?>
<style>
#add
{
	position: absolute;
	margin-left: 10%;
	margin-top: -2.9%;
}
.form_row{
        padding-bottom: 10px;
    }
</style>
<form id="frmsc">
<input name="model" value="applySCWithSequence" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog modal-lg">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                        <p>D`Great</p>
                    </div>
                </div>
            <center><b><h3 tag="title" class="modal-title">OB/Excuse Slipr</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
               	<div class="multiple">
               		<div class="form_row">
               		    <label class="field_name align_right">Will be approve by approver?</label>
               		    <div class="field no-search">
               		        <select class="form-control" name="allowApprover" id="allowApprover">
               		            <option value="1">YES</option> <!-- kapag yes, dadaan sa sequence approver -->
               		            <option value="0">NO</option> <!-- kapag no, deretso approved na -->
               		        </select>
               		    </div>
               		</div>
					<div class="form_row">
	                    <label class="field_name align_right">Date when Service Credit will be uses</label>
	                    <div class="field no-search" >
	                    		<div class='input-group date' id='datePickers' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                  <input type='text' class="form-control" size="16" class='date' name="date" id="date" type="text" value=""/>
                                  <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
	                    	<!-- <a  id="adddate" href="#" class="btn btn-default pull-center" ><i class='glyphicon glyphicon-plus'></i></a> -->
	                      <!--   <div class="input-group date" id='datePickers' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
	                            <input class="align_center" size="16" class='date' name="dates" id="dates" type="text" value="" readonly>
	                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
	                        </div>                      -->
							<span id='message' style='color:red'></span>
	                    </div>
	                </div>
	                <div class="form_row">
	                    <label class="field_name align_right">Employee</label>
	                    <div class="field">
	                        <select class="chosen col-md-4" id="employee" name="employee">
	                            <?
	                                $emplist = $CI->utils->getEmpListToCbo();

	                                $i = 0;
	                                # displayed employee list
	                                foreach ($emplist as $key => $value) {
	                                    if($i > 0){
	                            ?>
	                                    <option value="<?=$key?>"><?=$key ." - ". $value?></option>
	                            <?      } # end of if condition
	                                    $i += 1;
	                                } # end of foreach 
	                            ?>
	                        </select>
	                    </div>
	                </div>
	                
	                <div class="form_row">
	                    <div class="field"  style="padding-bottom: 10px;">
	                     <input type="checkbox" class="double-sized-cb" name="ishalfday" value="1">&nbsp;&nbsp; 
	                     <b>Check this if your leave to be applied is halfday</b>
	                    </div>
	                </div>
	                
					<div class="form_row" id="wrap_sched_affected" style="display: none;">
	                    <label class="field_name align_right" id="sched_affectedlabel">Check Schedules Affected</label>
	                    <div class="field" id="sched_affected">
	                        No Schedule     
	                    </div>
	                </div>
	              
	                <div class="form_row">
	                    <label class="field_name align_right " >Service Credit Date</label>
						<div class="field no-search">
							<table width='50%' id='table'>
								<tr>
									<td>
										<select class='chosen scdate' id='scdate' name='scdate'></select>
									</td>
									<td>
										<label class="field_name align_right">Days</label>
										<div class="field">
											<select class='chosen sc' id='sc' name='sc'>
												
											</select>
										</div>
									</td>					
									<td>
										<a  id="add" href="#" class="btn btn-default pull-right" style='display:none'><i class='glyphicon glyphicon-plus'></i></a>
									</td>
								</tr>
							</table>
	                    </div>
	                </div>
               	</div>

				<br>
                <div class="form_row">
                    <label class="field_name align_right">Remarks</label>
                    <div class="field no-search">
                        <textarea rows="3" class="form-control" name="remark" id="remark" placeholder="Reason"></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <span id="errormsg" hidden="" style="color: red; margin-right: 20px;"></span>
            <span id="loading" hidden=""></span>
            <span id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-success">Save</button>
            </span>
        </div>
    </div>
</div>
</form>
<script>
$(document).on('click','#adddate',function()
{
	// $("#datePicker").append("<div id='dates'></div>");
	var $creditRow = $('.multiple').last().clone();
	
	var scdateId = $creditRow.find('[name=scdate]').removeClass("chzn-done").chosen('destroy').removeAttr('id').uniqueId().attr('id');
	var scdaysId = $creditRow.find('[name=sc]').removeClass("chzn-done").chosen('destroy').removeAttr('id').uniqueId().attr('id');
	$creditRow.find('[name="date"]').removeAttr('id').uniqueId().datepicker({autoclose:true})
	.data('target','#'+scdateId);
	$creditRow.find('input[name=ishalfday]').uniqueId().data('targetdate','#'+scdaysId);
	// alert(scdaysId);
	// console.log($creditRow.find('input[name=ishalfday]').uniqueId().data('targetdate','#'+scdaysId));
	 //$("<div class='input-group date'  data-date-format='yyyy-mm-dd'><input class='align_center ' size='16' name='date'   type='text' readonly><span class='add-on'><i class='glyphicon glyphicon-calendar'></i></span></div> <div class='form_row'><div class='field'  style='padding-bottom: 10px;'> <input type='checkbox' class='double-sized-cb' name='ishalfday' value='1'>&nbsp;&nbsp;<b>Check this if your leave to be applied is halfday</b></div></div>");

	$(".content").last().after($creditRow);
	// append($datepicker);
	// $datepicker.datepicker(
	// {
	// 	autoclose:true
	// });
});

$("#datePicker,#datePickers").datetimepicker(
	{
		format: "YYYY-MM-DD"
	});


$(document).on('change','.sched_affected',function()
{

  $('.sched_affected').not(this).prop('checked',false);

});

	$('#myModal').unbind('change').on('change','input[name=ishalfday],input[name="date"],input[name=datesetto]', function(){
		var a = $(".date").attr('id');
		// console.log($(this).data('target'));
		//return;
	    if($('input[name=ishalfday]').is(":checked")){
	    	console.log($(this).data('targetdate'));
	    	var data = { "0.5": "0.5" };
	    	var list = $("#sc");
	    	list.find('option').remove();
	    	$.each(data, function(index, a) {
	    	  list.append("<option value='"+a+"'>"+a+"</option>");
	    	});
	    	list.trigger("liszt:updated");	
	    	$("#add").hide();
	        var start = $("#date").val();
	        // alert(start);
	        $("input[name=datesetto]").val(start);
	        // $("#datesetto").hide();
	        $('#wrap_sched_affected').show();
	        if(start != ''){

	                $.ajax({
	                   url      :   "<?=site_url("leave_/getEmployeeScheduleStartEnd")?>",
	                   type     :   "POST",
	                   data     :   {start:start,empID:$("#employee").val()},
	                   success  :   function(ret){
	                    var arr_sched = JSON.parse(ret);
	                    // console.log(arr_sched);

	                    var hrs = 0;
	                    var fromtime    = '',
	                        totime      = '',
	                        isAm        = '',
	                        isBoth      = '';
	                    $("#ndays").val(hrs);
	                    ///< append sched affected
	                    if($(arr_sched).size() > 0){
	                        $('#sched_affected').html("");

	                        for (var key in arr_sched) {

	                            var key_arr = key.split('|');
	                            fromtime = key_arr[0] ? key_arr[0] : '';
	                            totime   = key_arr[1] ? key_arr[1] : '';
	                            hrs      = key_arr[2] ? key_arr[2] : 0;
	                            isAm     = key_arr[3] ? key_arr[3] : 0;
	                            isBoth      = key_arr[4] ? key_arr[4] : 0;

	                            $('#sched_affected').append('<span class="col-md-3"><input type="checkbox" name="sched_affected[]" tag="schedule" class="sched_affected" fromtime="'+fromtime+'" totime="'+totime+'" hrs="'+hrs+'" isAm="'+isAm+'" isBoth="'+isBoth+'" days="0.5" value="'+fromtime+"|"+totime+'"> '+arr_sched[key]+'</span>');
	                            $("#sched_affected").show();
	                            $("#sched_affectedlabel").show();
	                        }
	                    }else $('#sched_affected').html("No Schedule"); $("#sched_affected").show();$("#sched_affectedlabel").show();

	                   }
	                });
	        }
	    }else{
	    	var data = {};
	    	var list = $("#sc");
	    	// var list = $($(this).data('target'));
	    	// console.log(list);
	    	list.find('option').remove();
	    	$.each(data, function(index, a) {
	    	  list.append("<option value='"+a+"'>"+a+"</option>");
	    	});
	    	list.trigger("liszt:updated");	
	        // $("#datesetto").show();
	        $('#wrap_sched_affected').hide();
	        // var start = $("input[name='datesetfrom']").val();
	        //     end   = $("input[name='datesetto']").val();
	        // countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
	    }
	});


// 	$("input[name='tag']").click(function(){
// 	alert("oye");
//    var name = $(this).attr("tag");
//    var value = $(this).val();
//    if(name == "schedule"){


//     if($(this).val() == value)  $(".sched_affected").attr("disabled",true).css("background","#EEEEEE").val("");
//     else                    $(".sched_affected").attr("disabled",false).css("background","transparent").val("");
 
//    }
// });


var serviceCreditUsed = 1;

$(".date").change(function(){
	var empid = $("#employee").val();
	$("#table tr:eq(1)").remove();
	$("#add").hide();
	updateServiceCreditDateList('scdate','sc',empid);
});

$("#employee").change(function(){
	var empid = $(this).val();
	$("#table tr:eq(1)").remove();
	$("#add").hide();
	updateServiceCreditDateList('scdate','sc',empid);
	$("input[name=ishalfday]").prop('checked',false);
	$("#sched_affected").hide();
	 $("#sched_affectedlabel").hide();
});
$
$("#scdate").change(function(){
	$("#table tr:eq(1)").remove();
	var available_sc = $(this).find(":selected").attr('available_sc');
	if(available_sc == 1)
	{
		var data = { "1": "1", "0.5": "0.5" };
		// $("#add").hide();
	}
	else if(available_sc > 0)
	{
		var data = { "0.5": "0.5" };
		// $("#add").show();
	}
	else
	{
		var data = {};
		// $("#add").hide();
	}
	
	serviceCreditUsed = 1;
	
	var list = $("#sc");
	// var list = $($(this).data('target'));
	list.find('option').remove();
	$.each(data, function(index, a) {
	  list.append("<option value='"+a+"'>"+a+"</option>");
	});
	list.trigger("liszt:updated");	
});

$("#sc").change(function(){

	if($(this).val() < 1)
	{
		if ($('input[name=ishalfday]').is(":checked")){
			$("#add").hide();
		}
		else
		{
			$("#add").show();
			remove();
		}
	}
	else
	{
		$("#add").hide();
		remove();
	}
	
});		


$("#add").click(function(){
	if(serviceCreditUsed == 1)
	{
		var dates = $(this).closest("scdate").html()
		var tr = "<tr><td><select class='chosen scdate' id='scdate2' name='scdate2'></select></td><td><label class='field_name align_right'>Days</label><div class='field'><select class='chosen sc' id='sc2' name='sc2'><option>0.5</option></select></div></td><td><a class='btn blue' id='remove' href='#' class='btn btn-default' onclick='remove()'><i class='glyphicon glyphicon-remove-sign'></i></a></td></tr>";
		$(this).closest("table").append(tr);
		serviceCreditUsed += 1;
		
		var $options = $("#scdate option:not(:selected)").clone();
		$('#scdate2').append($options);
		
		$(".chosen").chosen();
		$("#add").hide()
	}
});

function remove(){
	serviceCreditUsed = 1;

	$("#table tr:eq(1)").remove();
}

$("#save").unbind("click").bind("click",function(){
	var iscontinue = true;
	if($("#sc").val() == null){
		alert('Select valid date.');
		iscontinue = false;
	}
	
	if($("#date").val() == "")
	{
		$("#message").show().html("This is required!");
		iscontinue = false;
	}
	
	if($("#scdate").val() == "")
	{
		$("#errormsg").html("Please complete the form!").show();
		iscontinue = false;
	}
	else
	{
		if($("#scdate2").length > 0)
		{
			if($("#scdate2").val() == "")
			{
				$("#errormsg").html("Please complete the form!").show();
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

	if( $("#sc").val() == 0.5){
	    if($('#wrap_sched_affected').find('input[name="sched_affected[]"]:checked').length < 1){
	        alert('Please select schedule affected for half day.');
	        iscontinue = false;
	   
	    }
	}
	if( $("#sc").val() == ""){  
	        $("#errormsg").html("Please complete the form!").show();
	        iscontinue = false;
	}
	
	
	
	if(iscontinue)
	{
		var checkedsched = 0;
		$('.sched_affected').each(function()
		{   
			if ($(this).is(":checked")) {
				checkedsched += (+$(this).attr('days'));
				
			}

		});

		if (checkedsched != $("#sc").val() && $('input[name=ishalfday]').is(":checked"))
		 {
			$("#errormsg").html("Applied Days is wrong! Check the data!").show();
		}
		else
		{
			var form_data   =   $("#frmsc").serialize();
			// console.log(form_data);return;
			$("#saving").hide();
		    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
		    $.ajax({
		       url      :   "<?=site_url("service_credit_/saveSCAppUseHR")?>",
		       type     :   "POST",
		       data     :   form_data,
		       success  :   function(msg){
		        $(function(){
		          $("#close").click();
		        });
		         $("#close").click();
		         location.reload();
		        alert(msg);
		        console.log(msg);
		        // location.reload();
		        // loadscuhistory();
		       }
		    });
    	}
	}
	
});

$(".chosen").chosen();


function updateServiceCreditDateList(dateField,dayField,empid)
{
	$("#"+dateField).html("").trigger("liszt:updated");	
	$("#"+dayField).val("");	
		
	$.ajax({
		url      :   "<?=site_url("service_credit_/getSCDatesWithAvailableHR")?>",
		type     :   "POST",
		data     :   {empid:empid},
		dataType : 	'html',
		success  :   function(result){
			
			$("#"+dateField).html(result).trigger("liszt:updated");	
		}
	});
}
</script>