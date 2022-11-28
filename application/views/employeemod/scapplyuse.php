<?php
	$CI = &get_instance();
	$CI->load->model("service_credit");
	$datetoday = date("d-m-Y");
	$employeeid = $this->session->userdata("username");
	$date = $sc_date = $sc_days = $remarks = "";
	if(isset($code)){
		$data = $CI->service_credit->getSCUDetailsEdit($code);
		foreach($data as $key => $value){
			$date = $value['date'];
			$sc_date = $value['service_credit_date_use'];
			$sc_days = $value['service_credit_use'];
			$remarks = $value['remark'];
		}
	}

	$sc_date = substr($sc_date, 0, -1);
	$sc_days = substr($sc_days, 0, -1);

	# for ica-hyperion 21185
	# >
	$query = $CI->service_credit->getEmpSC();
	$available_sc = 0;
	if($query) foreach ($query as $res) $available_sc += $res->available_sc;
	# end for ica-hyperion 21185
?>
<style>
#add
{
	position: absolute;
	margin-left: 10%;
	margin-top: -2.9%;
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
            <center><b><h3 tag="title" class="modal-title">Use Service Credit</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
               	<div class="main-multiple">
					<div class="form_row" id="main-multiple-div" tag="div_date" scnum="1">
	                    <label class="field_name align_right">Date when Service Credit will be used</label>
	                    <div class="field no-search" >
	                    	<div class='input-group date' id='datePicker' data-date="<?=($date) ? $date : $datetoday ?>" data-date-format="yyyy-mm-dd">
			                    <input type='text' class="form-control" name="date" id="date" value="<?=($date) ? $date : $datetoday ?>"/>
			                    <span class="input-group-addon">
			                        <span class="glyphicon glyphicon-time"></span>
			                    </span>
			                </div>
	                    	<a  id="adddate" href="#" class="btn btn-default pull-center" onclick="addDateServiceCredit()" ><i class='glyphicon glyphicon-plus'></i></a>
	                    	<span id='message' style='color:red'></span>
	                    </div>
	                </div>
	                <div class="form_row" id="main-multiple-div" tag="div_halfday" scnum="1">
	                    <div class="field"  style="padding-bottom: 10px;">
	                     <input type="checkbox" class="double-sized-cb ishalfday" name="ishalfday" value="1">&nbsp;&nbsp; 
	                     <b>Check this if your leave to be applied is halfday</b>
	                    </div>
	                </div>

					<div class="form_row main-multiple-div" id="wrap_sched_affected" style="display: none;"  tag="div_halfday_display" scnum="1">
	                    <label class="field_name align_right">Check Schedules Affected</label>
	                    <div class="field" id="sched_affected">
	                        No Schedule     
	                    </div>
	                </div>
	              
	                <div class="form_row" id="main-multiple-div" tag="div_sc_date" scnum="1">
	                    <label class="field_name align_right " >Service Credit Date</label>
						<div class="field no-search">
							<table width='50%' id='table'>
								<tr>
									<td>
										<select class='chosen scdate' id='scdate' name='scdate'>
											<?php if($sc_date) {?>
												<option value="<?= $sc_date ?>"><?= $sc_date ?></option>
											<?php } ?>
										</select>
									</td>
									<td>
										<label class="field_name align_right">Days</label>
										<div class="field">
											<select class='chosen sc' id='sc' name='sc'>
											<?php if($sc_date) {?>
												<option value="<?= $sc_days ?>"><?= $sc_days ?></option>
											<?php } ?>
											</select>
										</div>
									</td>					
									<td>
										<a  id="add" href="#" class="btn btn-default pull-right add" style='display:none'><i class='glyphicon glyphicon-plus'></i></a>
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
                        <textarea rows="3" class="form-control" name="remark" id="remark" placeholder="Reason"><?= $remarks?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <? if(!isset($code)){ ?>
	        <div class="modal-footer">
	            <span id="errormsg" hidden="" style="color: red; margin-right: 20px;"></span>
	            <span id="loading" hidden=""></span>
	            <span id="saving">
	                <button type="button" class="btn btn-success button_save_modal">Save</button>
	                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
	            </span>
	        </div>
	    <? } ?>
    </div>
</div>
</form>
<script>
	
// for ica-hyperion 21185
// by justin (with e)
// > for adding a multiple applying service credit..
var available_sc_date = [];
var sel_avail_sc_date = [];
function loadAvailableSCDate(formdata = {}){
	$.ajax({
		url : "<?=site_url("service_credit_/findAvailableSCDates")?>",
		type : "POST",
		dataType : "json",
		data : formdata,
		async : false,
		success : function(result){
			available_sc_date = result;
			<? if(!isset($code)){ ?>
				var scnum = $("#main-multiple-div").attr('scnum');
				updateServiceCreditDateList('scdate','sc', scnum);
			<? } ?>

		}
	});
}
loadAvailableSCDate();

var service_credit = 1;
var append_div = "";
function addDateServiceCredit(){
	var arr_div = ["div_date", "div_halfday", "div_halfday_display", "div_sc_date"];
	var main_mutiple = $(".main-multiple");
	
	if(service_credit == <?=$available_sc?>) {
		alert("You reach the maximum Available Service Credit..");
		return;
	}

	service_credit += 1;

	for(tag in arr_div){
		var append_id = "";
		var append_style = "";
		var append_class = "";
		var append_remove_div = "";
		var id = "sub-multiple-div";
		var idOrClass = "id='main-multiple-div'";

		if(tag == 2){
			idOrClass = "class='main-multiple-div'";
			append_class = id;
			id = "wrap_sched_affected";
			append_style = "style='display: none;'";
		}

		append_div = "";
		append_div = "<div class='form_row "+ append_class +"' id='"+ id +"' "+ append_style +" tag='"+ arr_div[tag] +"' scnum='"+ service_credit +"'>";
		append_div += $("div[scnum='1'][tag='"+ arr_div[tag] +"']").html();
		append_div += "</div>";

		main_mutiple.append(append_div);
	}

	$("div[tag='div_date'][scnum='"+ service_credit +"']").find(".no-search").html($("div[tag='div_date'][scnum='"+ service_credit +"']").find(".no-search").html() + "<a id=\""+ service_credit +"\" href=\"#\" class=\"btn btn-danger pull-center red\" onclick=\"removeDateServiceCredit(this.id)\"><i class=\"glyphicon glyphicon-trash\"></i></a>");

	reloadMultipleClassesAndFunction();
}

function removeDateServiceCredit(scnum){
	$("div[scnum='"+ scnum +"']").remove();
	service_credit -= 1;
}

function reloadMultipleClassesAndFunction(){


$("#datePicker,#datePickers").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();

	//$(".chosen").chosen();

	reloadFunction();
}
reloadMultipleClassesAndFunction();
// end for ica-hyperion 21185




$(document).on('change','.sched_affected',function()
{
  var scnum = $(this).closest("div[tag='div_halfday_display']").attr('scnum');
    
  if(scnum) $("div[tag='div_halfday_display'][scnum='"+ scnum +"']").find('.sched_affected').not(this).prop('checked',false);

});


function isAlreadySelectedDate(cdate, avail_sc, scnum){
	var iscontinue = true;
	if(sel_avail_sc_date.length > 0){
		
		var isExistOnAnotherSCDate = false;
		for(i in sel_avail_sc_date){
			if(i != scnum && cdate == sel_avail_sc_date[i]["date"]){
				isExistOnAnotherSCDate = true;

				break;
			}
		}

		if(isExistOnAnotherSCDate) iscontinue = false;
		else{
			if(sel_avail_sc_date.indexOf(scnum) == -1){
				sel_avail_sc_date[scnum] = [];
			}

			sel_avail_sc_date[scnum]["date"] 	 = cdate;		
			sel_avail_sc_date[scnum]["avail_sc"] = avail_sc;				
		}
	}else{
		sel_avail_sc_date[scnum] = [];
		sel_avail_sc_date[scnum]["date"] 	 = cdate;		
		sel_avail_sc_date[scnum]["avail_sc"] = avail_sc;		
	}
	
	return iscontinue;
}

var serviceCreditUsed = 1;

function reloadFunction(){
	$(".date").unbind('change').change(function(){
		$("#table tr:eq(1)").remove();
		$("#add").hide();
		var scnum = $(this).closest("div[tag='div_date']").attr('scnum');
		updateServiceCreditDateList('scdate','sc', scnum);
	});

	$(".scdate").unbind('change').change(function(){
		var scnum = $(this).closest("div[tag='div_sc_date']").attr('scnum');
		var value = $(this).val();
		var available_sc = $(this).find(":selected").attr('available_sc');
		

		var iscontinue = isAlreadySelectedDate(value, available_sc, scnum);
		if(!iscontinue){
			alert("This date was already selected");
		}

		$("#table tr:eq(1)").remove();
		if(available_sc >= 1 && iscontinue)
		{
			var data = { "1": "1", "0.5": "0.5" };
			// $("#add").hide();
		}
		else if(available_sc <=1 && iscontinue)
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
		
		var list = $("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("#sc");
		// var list = $($(this).data('target'));
		list.find('option').remove();
		$.each(data, function(index, a) {
		  list.append("<option value='"+a+"'>"+a+"</option>");
		});
		list.trigger("liszt:updated");	
	});

	$(".sc").change(function(){
		var scnum = $(this).closest("div[tag='div_sc_date']").attr('scnum');
		if($(this).val() < 1)
		{
			if ($("div[tag='div_halfday'][scnum='"+ scnum +"']").find('input[name=ishalfday]').is(":checked")){
				$("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("#add").hide();
			}
			else
			{
				$("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("#add").show();
				remove();
			}
		}
		else
		{
			$("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("#add").hide();
			remove();
		}
		
	});	

	$(".add").click(function(){
		var scnum = $(this).closest("div[tag='div_sc_date']").attr('scnum');
		if(serviceCreditUsed == 1)
		{
			var dates = $(this).closest("scdate").html();
			var tr = "<tr><td><select class='chosen scdate2' id='scdate2' name='scdate2'></select></td><td><label class='field_name align_right'>Days</label><div class='field'><select class='chosen sc' id='sc2' name='sc2'><option>0.5</option></select></div></td><td><a class='btn blue' id='remove' href='#' class='btn btn-default' onclick='remove()'><i class='glyphicon glyphicon-remove-sign'></i></a></td></tr>";
			$(this).closest("table").append(tr);
			serviceCreditUsed += 1;
			

			var scdate1 = $("div[tag='div_sc_date'][scnum='"+ scnum +"']").find(".scdate");
			//var $options = $("#scdate option:not(:selected)").clone();
			$("div[tag='div_sc_date']").find('.scdate2').html(scdate1.html());
			$("div[tag='div_sc_date']").find('.scdate2').val(scdate1.val());
			
			
			//$(".chosen").chosen();
			$(this).hide()
		}
	});

	// $('#myModal').unbind('change').on('change','input[name=ishalfday], .ishalfday,input[name=date],input[name=datesetto]', function(){
	$('#myModal, #modal-view').unbind('change').on('change','.ishalfday,input[name=date],input[name=datesetto]', function(){
		var a = $(".date").attr('id');
		var scnum = $(this).closest("div[tag='div_halfday'], div[tag='div_date']").attr('scnum');
		

		// console.log($(this).data('target'));
		//return;
	    if($("div[tag='div_halfday'][scnum='"+ scnum +"']").find('input[name=ishalfday]').is(":checked")){
	    	//console.log($(this).data('targetdate'));
	    	var data = { "0.5": "0.5" };
	    	var list = $("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("#sc");
	    	list.find('option').remove();
	    	$.each(data, function(index, a) {
	    	  list.append("<option value='"+a+"'>"+a+"</option>");
	    	});
	    	list.trigger("liszt:updated");	
	    	$("#add").hide();
	        var start = $("div[tag='div_date'][scnum='"+ scnum +"']").find("#date").val();
	        // alert(start);
	        $("input[name=datesetto]").val(start);
	        // $("#datesetto").hide();
	        //$('#wrap_sched_affected').show();
	        $("div[id='wrap_sched_affected'][tag='div_halfday_display'][scnum='"+ scnum +"']").show();
	        if(start != ''){

	                $.ajax({
	                   url      :   "<?=site_url("leave_/getEmployeeScheduleStartEnd")?>",
	                   type     :   "POST",
	                   data     :   {start:start},
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
	                        $("div[id='wrap_sched_affected'][tag='div_halfday_display'][scnum='"+ scnum +"']").find('#sched_affected').html("");

	                        for (var key in arr_sched) {

	                            var key_arr = key.split('|');
	                            fromtime = key_arr[0] ? key_arr[0] : '';
	                            totime   = key_arr[1] ? key_arr[1] : '';
	                            hrs      = key_arr[2] ? key_arr[2] : 0;
	                            isAm     = key_arr[3] ? key_arr[3] : 0;
	                            isBoth      = key_arr[4] ? key_arr[4] : 0;

	                            $("div[id='wrap_sched_affected'][tag='div_halfday_display'][scnum='"+ scnum +"']").find('#sched_affected').append('<span class="col-md-3"><input type="checkbox" name="sched_affected[]" tag="schedule" class="sched_affected" fromtime="'+fromtime+'" totime="'+totime+'" hrs="'+hrs+'" isAm="'+isAm+'" isBoth="'+isBoth+'" days="0.5" value="'+fromtime+"|"+totime+'"> '+arr_sched[key]+'</span>');
	                        }
	                    }else $("div[id='wrap_sched_affected'][tag='div_halfday_display'][scnum='"+ scnum +"']").find('#sched_affected').html("No Schedule");

	                   }
	                });
	        }
	    }else{
	    	var data = {};
	    	var list = $("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("#sc");
	    	// var list = $($(this).data('target'));
	    	// console.log(list);
	    	list.find('option').remove();
	    	$.each(data, function(index, a) {
	    	  list.append("<option value='"+a+"'>"+a+"</option>");
	    	});
	    	list.trigger("liszt:updated");	
	        // $("#datesetto").show();
	        $("div[id='wrap_sched_affected'][tag='div_halfday_display'][scnum='"+ scnum +"']").hide();
	        // var start = $("input[name='datesetfrom']").val();
	        //     end   = $("input[name='datesetto']").val();
	        // countDaysWithinSchedule(start, end); ///< checks employee schedule first for applicable number of days
	    }
	});
}



function remove(){
	serviceCreditUsed = 1;

	$("#table tr:eq(1)").remove();
}

function array_key_exist(needle, haystack){
	var isExist = false;

	for(key in haystack){
		if(key == needle) isExist = true;
	}

	return isExist;
}

function checkDateIsExist(val_date, name, sc_request){
	isExist = false;

	for(scnum in sc_request){
		if(val_date == sc_request[scnum][name]) isExist = true;
	}

	return isExist;
}


function getFormData(){
	var iscontinue = true;
	var sc_request = {};
	var arr_div = {
					"div_date" 			  : {
											  "date" : "input",
											  "error": "Date when Service Credit will be used"
											}, 
					"div_halfday" 		  : {
											  "ishalfday" : "input",
											  "error"	  : ""
										 	},  
					"div_halfday_display" : {
											  "sched_affected[]" : "input",
											  "error"			 : "Check Schedules Affected"
											}, 
					"div_sc_date" 		  : {
											  "scdate"  : "select",
											  "scdate2" : "select",
											  "sc"      : "select",
											  "sc2"     : "select",
											  "error"   : "Service Credit Date"
											}
				  };

	var errormsg = "";
	for(tag in arr_div){
		var scnum = 1;
		$("div[tag='"+ tag +"']").each(function(){

			if(!array_key_exist(scnum, sc_request)){
				sc_request[scnum] = {};
			}

			for(name in arr_div[tag]){
				if($(this).find(arr_div[tag][name] + "[name='"+ name +"']").val() != undefined && name != "error"){
					var value = "";
					if(name == "ishalfday" || name == "sched_affected[]"){
						value = ($(this).find(arr_div[tag][name] + "[name='"+ name +"']:checked").val()) ? $(this).find(arr_div[tag][name] + "[name='"+ name +"']:checked").val() : "";
					}else{
						value = $(this).find(arr_div[tag][name] + "[name='"+ name +"']").val();
					}

					// check here the form data
					if(!value && name != "ishalfday" && name != "sched_affected[]" && iscontinue){
						errormsg = "Please select a "+ arr_div[tag]["error"];
						iscontinue = false;
					}

					if(checkDateIsExist(value, name, sc_request) && name != "ishalfday" && name != "sched_affected[]" && name != "sc" && name != "sc2" && iscontinue){
						errormsg = "The date in '"+ arr_div[tag]["error"] +"' is exist on other request..";
						iscontinue = false;
					}

					if(((name == "scdate" && !$(this).find(arr_div[tag][name] + "[name='sc']").val()) || (name == "scdate2" && !$(this).find(arr_div[tag][name] + "[name='sc2']").val())) && iscontinue){
						errormsg = "Please select the days in '"+ arr_div[tag]["error"] +"'";
						iscontinue = false;	
					}

					if(name == "ishalfday" && value && !$("div[tag='div_halfday_display'][scnum='"+ scnum +"']").find(arr_div[tag][name] + "[name='sched_affected[]']:checked").val() && iscontinue){
						errormsg = "Please select a "+ arr_div["div_halfday_display"]["error"];
						iscontinue = false;
					}

					if(name == "ishalfday" && value && $("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("select[name='sc']").val() != "0.5" && iscontinue){
						errormsg = "Please select the 0.5 in days";
						iscontinue = false;
					}

					// end of checking form data

					sc_request[scnum][name.replace("[]","")] = value;
					if(name == "sched_affected[]" && !sc_request[scnum]["ishalfday"]){
						sc_request[scnum]["sched_affected"] = "";
					}
				}				
			}

			scnum += 1;
		});
	}
	
	return [iscontinue, sc_request, errormsg];
}


$(".button_save_modal, #button_save_modal").unbind("click").bind("click",function(){
	var form_data = {};
	var iscontinue, sc_request, errormsg; 
	result = getFormData();
	[iscontinue, sc_request, errormsg] = result;
	
	if(errormsg) alert(errormsg);
	
	if(iscontinue && !$("#remark").val()){
		alert("Please enter a remarks");
		return;
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
	var employeeid = "<?= $employeeid ?>";
	var scdays = $("#sc").val();
	var scbalance = validateDays(employeeid);
    var total = scbalance - scdays;
    if(total < 0){
        if(scbalance.trim() == "0.5") alert('You only have 0.5 available balance. Application failed');
        else alert('Insufficient service credit. Application failed');
        iscontinue = false;
    }

	if(iscontinue){
		form_data = {
			employee   : "<?=$employeeid?>",
			sc_request : sc_request,
			remark 	   : $("#remark").val(),
			code : "<?= isset($code) ? $code : '' ?>"
		};
		/*$("#saving").hide();
	    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");*/
	    $.ajax({
	       url      :   "<?=site_url("service_credit_/saveSCAppUseWithMultipleSCDate")?>",
	       type     :   "POST",
	       data     :   form_data,
	       success  :   function(msg){
	        $(function(){
	          $("#close").click();
	        });
	         $("#close").click();
	        alert(msg);
	        location.reload();
	        loadscuhistory();
	       }
	    });
	}

});

//$(".chosen").chosen();
function updateServiceCreditDateList(dateField, dayField, scnum)
{
	var option = "";
	$("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("#"+dateField).html("").trigger("liszt:updated");	
	$("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("#"+dayField).val("");	

	option = makeDateOption(available_sc_date);
	
	$("div[tag='div_sc_date'][scnum='"+ scnum +"']").find("#"+dateField).html(option).trigger("liszt:updated");	
}

function makeDateOption(date_list){
	var option = "<option value=''>Select Date</option>";
	var iscontinue;

	for(id in date_list){
		iscontinue = true;

		if(iscontinue){
			for(cdate in date_list[id]){
				var value = cdate;
				var available_sc = date_list[id][cdate]["available_sc"];
				var display_date = date_list[id][cdate]["cdate"];

				option += "<option value='"+ value +"' available_sc='"+ available_sc +"'>"+ display_date +"</option>";
			}
		}
	}

	return option;
}

function validateDays(employeeid){
    var sc_days = 0;
    if(employeeid){
        $.ajax({
            type: "POST",
            async: false,
            url: "<?= site_url('service_credit_/validateSCURequest') ?>",
            data: {
                    employeeid : employeeid
                  },
            success:function(response){
                sc_days = response;
            }
        });
    }
    return sc_days;
}

</script>