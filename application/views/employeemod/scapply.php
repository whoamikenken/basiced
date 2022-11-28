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
            <center><b><h3 tag="title" class="modal-title">Apply Service Credit</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
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
                    <label class="field_name align_right">Service Credit Date</label>
                    <div class="field">
                        <div class="col-md-12"style="padding-left: 0px;">
                          <div class="col-md-6" style="padding-left: 0px;">
                          <div class='input-group date' id="ldfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input type='text' class="form-control" size="16" name="date" id="date" type="text" value="<?=isset($dateInitial)?$dateInitial:''?>"/>
                            <span class="input-group-addon">
                                  <span class="glyphicon glyphicon-calendar"></span>
                            </span>
                          </div>
                        </div>
                      <div class="col-md-6">
                        <div class='input-group date' id="ldto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                          <input type='text' class="form-control" size="16" name="date1" id="date1" type="text" value="<?=isset($dateInitial)?$dateInitial:''?>"/>
                          <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar"></span>
                          </span>
                        </div>
                      </div>
                        </div>
                    </div>
                </div><br>
				
				<div class="form_row">
                    <label class="field_name align_right">Service Credit</label>
                    <div class="field no-search">
                        <input type='text' name="sc" id="sc" value="1" class="form-control" readonly/>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" class="form-control" name="reason" id="reason" placeholder="Reason"></textarea>
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
$(document).ready(function(){
    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });
});

var sc_day_list = {};
$("input[name='date'], input[name='date1']").on('change', function(){
    var from_date = $("input[name='date']").val();
    var to_date   = $("input[name='date1']").val();
    var day_mode  = $("#dayMode").val();

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
                dayMode : ($("#dayMode").val() == "whole") ? 0 : 1
               },
        dataType : "json",       
        success : function(day_list){
            console.log(day_list);

            var count = 0;
            for(day in day_list) count += day_list[day];
            
            $("input[name='sc']").val(count);
            sc_day_list = day_list;
        }
    });
});

$("#save").unbind("click").bind("click",function(){
	$("#errormsg").html('');
    var employeeid = "<?= $employeeid ?>";
    var isContinue = true;
    var scdays = $("#sc").val();
    var scbalance = validateDays(employeeid);
    var total = scbalance - scdays;
    if(total < 0){
        if(scbalance.trim() == "0.5") alert('You only have 0.5 available balance. Application failed');
        else alert('Insufficient service credit. Application failed');
        return;
    }
	var form_data   =   $("#frmsc").serialize();
	
    if($("input[name='date']").val() == ""){
        $("#errormsg").show().html("Service Credit Date is required!.");
        return;
    }
    if(!$("input[name='sc']").val() || $("input[name='sc']").val() == 0){
        $("#sc").css("border-color","red").attr("placeholder", "This field is required!.").focus();
        $("#errormsg").show().html("Service Credit Date is invalid!.");
        return;
    }
    if($("#dayMode").val() == "whole"){
        if ($("#date").val() == "" || $("#date1").val() == "" ) {
            $("#errormsg").show().html("One of date is empty!.");
            return;
        }
    }
    if($("#reason").val() == ""){
        $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
        $("#errormsg").show().html("Reason is required!.");
        return;
    }

    $("#saving").hide();
    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("service_credit_/saveSCApplication")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        //console.log(msg);
        alert(msg);
        $(function(){
        if (typeof loadsc !== 'undefined') {
              loadsc();
        }
        $("#close").click();
        });
        if (typeof loadschistory !== 'undefined') {
          loadschistory("");
        }
        // $('#search').click();
       }
    });
});


$("#dayMode").change(function(){
	var dayMode = $(this).val();
	if(dayMode == 'whole')
	{
		$("#sc").val(1);
        $("#datePickers").show();
         $("#date").val('');
        $("#date1").val('');
	}
	else
	{
		$("#sc").val(0.5);
	   $("#datePickers").hide();
        $("#date").val('');
        $("#date1").val('');
    }
});

function validateDays(employeeid){
    var sc_days = 0;
    if(employeeid){
        $.ajax({
            type: "POST",
            async: false,
            url: "<?= site_url('service_credit_/validateSCRequest') ?>",
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

$(".chosen").chosen();
</script>