<?
/**
* @author justin (with e)
* @copyright 2018
*/
?>

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
			<center><b><h3 tag="title" class="modal-title">Service Credit</h3></b></center>
        </div>
        <form id="frm-sc">
        <input type="hidden" name="empid" value="<?=$employeeid?>">
        <div class="modal-body">
        	<div class="form_row">
		        <label class="field_name align_right">Date From</label>
		        <div class="field">
		        	<div class="col-md-6">
		        		<div class='input-group date' id="dfrom" data-date="<?=date("Y-m-d")?>" data-date-format="yyyy-mm-dd">
	                    <input type='text' class="form-control" name="dfrom" value="" />
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
		        	</div>
		        	<div class="col-md-6">
		        		<div class='input-group date dfrom-hide' id="dto" data-date="<?=date("Y-m-d")?>" data-date-format="yyyy-mm-dd">
	                    <input type='text' class="form-control" name="dto" value="" />
	                    <span class="input-group-addon">
	                        <span class="glyphicon glyphicon-calendar"></span>
	                    </span>
	                </div>
		        	</div>
		        </div>
		    </div>

		    <br><br>
		    <div class="form_row form-margin">
		        <label class="field_name align_right">No. days.</label>
		        <div class="field">
		        	<div class="col-md-6">
		        		<input class="input-group form-control" type="text" name="nodays" value="0" readonly>
		        	</div>
		        	<div class="col-md-6">
		        		<input type="checkbox" class="double-sized-cb" name="halfday" id="halfday" value="1">&nbsp;&nbsp;<b>Check this if request is halfday</b>
		        	</div>
		        </div>
		    </div>
    	</div>
    	</form>
    	<div class="modal-footer">
		    <div id="loading" hidden>Loading... Please wait...</div>
		    <div id="saving">
		        <button type="button" id="btn-close" class="btn btn-danger" data-dismiss="modal">Close</button>
		        <button type="button" id="btn-save" class="btn btn-primary">Save</button>
		    </div>
		</div>
</div>
<script type="text/javascript">
	$("input[name='halfday']").unbind("click").click(function(){
		if($(this).is(":checked")){
			//$("input[name='nodays']").val("0.5");
			$(".dfrom-hide").hide();
		}else{
			$("input[name='dto']").val("");
			$("input[name='nodays']").val("");
			$(".dfrom-hide").show();
		}
	});

	$("input[name='dfrom'], input[name='dto']").change(function(){
		var from_date = $("input[name='dfrom']").val();
	    var to_date   = (!$("input[name='dto']").val()) ? $("input[name='dfrom']").val() : $("input[name='dto']").val();
	    var day_mode  = ($("input[name='halfday']").is(":checked")) ? "half" : "whole";

	    if((!from_date || !to_date) && day_mode == 'whole') return;
	    if(from_date > to_date && day_mode == 'whole'){
	        alert("Invalid date");
	        return;
	    }

	    if (day_mode == 'half') to_date = from_date;

	    $.ajax({
	        url : "<?=site_url("service_credit_/getListOfAvailableSCDate")?>",
	        type : "POST",
	        data : {
	                fdate   : from_date,
	                tdate   : to_date,
	                empid   : "<?=$employeeid?>",
	                dayMode : (day_mode == "whole") ? 0 : 1
	               },
	        dataType : "json",       
	        success : function(day_list){
	            console.log(day_list);

	            var count = 0;
	            for(day in day_list) count += day_list[day];
	            
	            $("input[name='nodays']").val(count);
	            sc_day_list = day_list;
	        }
	    });
	});

	function validateForm(){
		var is_continue = true;

		if(!$("input[name='dfrom']").val()){
			alert("Date From is required.");
			is_continue = false;
		}

		if(is_continue && !$("input[name='halfday']").is(":checked") && !$("input[name='dto']").val()){
			alert("Date To is required.");
			is_continue = false;
		}

		if($("input[name='nodays']").val() == 0 && is_continue){
			alert("No Days is applied.");
			is_continue = false;
		}

		return is_continue;
	}

	$("#btn-save").unbind("click").click(function(){
		var is_continue = validateForm();

		if(is_continue){
			$("#loading").show();
			$("#saving").hide();
			var formdata = $("#frm-sc").serialize();

			$.ajax({
				url : "<?=site_url("service_credit_/saveSCDate")?>",
				type : "POST",
				data : formdata,
				success : function(response){
					$("#loading").hide();
					$("#saving").show();
					
					alert(response.trim());
					if(response.trim() == "Successfully saved."){
						$("#btn-close").click();
						displayAvailableSC();
					}
				}
			});
		}
	});

	$(".date").datetimepicker({
	    format: "DD-MM-YYY"
	});
</script>