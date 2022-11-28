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
				<td  rowspan="2" width="10%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
              
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
                    <div class="field no-search">
                         <div class="input-group date" id='datePicker' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="date" id="date" type="text" value="<?=isset($dateInitial)?$dateInitial:''?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <div class="input-group date" id='datePickers' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="date1" id="date1" type="text" value="<?=isset($dateInitial)?$dateInitial:''?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
						<span id='message' style='color:red'></span>
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
                        <textarea rows="4" style="width: 90%;resize: none;" name="reason" id="reason" placeholder="Reason"></textarea>
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
    $("#datePicker,#datePickers").datepicker({
        autoclose: true,
        todayBtn : true
    });
});

$("input[name='date'], input[name='date1']").on('change', function(){
    var from_date = $("input[name='date']").val();
    var to_date   = $("input[name='date1']").val();

    if(!from_date || !to_date) return;
    if(from_date > to_date){
        alert("Invalid date");
        return;
    }

    $.ajax({
        url : "<?=site_url("service_credit_/countAvailableSCDate")?>",
        type : "POST",
        data : {
                fdate : from_date,
                tdate : to_date,
                empid : "<?=$employeeid?>"
               },
        success : function(result){
            $("input[name='sc']").val(result);
        }
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
    }
    else{
        if ($("#dayMode").val() == "whole") {
            if ($("#date").val() == "" || $("#date1").val() == "" ) {
                $("#errormsg").show().html("One of date is empty!.");
                return false; 
            }
            else
            {
                $("#saving").hide();
                $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
                $.ajax({
                   url      :   "<?=site_url("service_credit_/saveSCApp")?>",
                   type     :   "POST",
                   data     :   form_data,
                   success  :   function(msg){
                    // console.log(msg);
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
            }      
        
        }
        else
        {
            $("#saving").hide();
            $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
            $.ajax({
               url      :   "<?=site_url("service_credit_/saveSCApp")?>",
               type     :   "POST",
               data     :   form_data,
               success  :   function(msg){
                console.log(msg);
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
        }
    }
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

$(".chosen").chosen();
</script>