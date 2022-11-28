<?php

/**
 * @author Justin
 * @copyright 2016
 */
$job = $this->input->post("job");
$id  = $this->input->post("idkey");
$mng = $this->input->post("manage") ? $this->input->post("manage") : false;
$base_id = "";

if($id){
    $query = $this->employeemod->loadseminarappdata($id,$mng);
    if($query->num_rows() > 0){
        foreach($query->result() as $row){
            if($this->input->post("mod") == "emph")  $aid = "";
            else                $aid = $row->aid;
            $eid = $row->eid;
            $lname = $row->lname;
            $fname = $row->fname;
            $mname = $row->mname;
            $pos   = $row->epos;
            $edept = $row->edept;
            $purpose    = $row->purpose;
            $course     = $row->course;
            $dfrom      = $row->dfrom;
            $dto        = $row->dto;
            $tstart     = date('h:i A',strtotime($row->tstart));;
            $tend       = date('h:i A',strtotime($row->tend));
            $days       = $row->nodays;
            $paid       = $row->paiddays;
            $pwdApproved       = $row->paiddays_approved;
            $cfee       = $row->coursefee;
            $cfeeApproved       = $row->coursefee_approved;
            $meal       = $row->meal;
            $mealApproved       = $row->meal_approved;
            $transpo    = $row->transportation;
            $transpoApproved    = $row->transportation_approved;
            $hotel      = $row->hotel;
            $hotelApproved      = $row->hotel_approved;
            $othermicellaneous      = $row->othermiscellaneous;
            $othermiscellaneousApproved      = $row->othermiscellaneous_approved;
            $totalcostApproved  = $row->totalcost_approved;
            $totalcost  = $row->totalcost;
            $venue      = $row->venue;
            $statement  = $row->statement;
            $status     = $row->status;
            $speaker    = $row->speaker;
            $miscellaneous = $row->miscellaneous;
            $base_id = $row->base_id;
        }
    }
$isdisabled = "disabled";
$isReadonly = "";
$expID = explode("-", $id);
$idx = $expID[0];
$id = $expID[1];
$r = $this->employee->getBudgetOff($this->session->userdata("username"));
if($r != "passed" || $status != "PENDING"){ //IDENTIFY IF THE USER IS NOT THE BUDGET OFFICER
	$isReadonly = "readonly";
}
?>
<style>
.modal{
    width: 50%;
    left: 0;
    right: 0;
    margin: auto;
}
</style>
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="70px"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td colspan="2"><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>ATTENDANCE TO PROFESSIONAL DEVELOPMENT PROGRAMS</strong></td>
                    <td><a href="<?php echo site_url("leave_/viewSeminarInvitation?id=$base_id")?>" target="_blank"><span class="pull-right" style="color: #6A1B9A;text-decoration: underline;"><b>View Official Invitation<b></span></a></td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Name</label>
                <div class="field">
                    <span style="line-height: 5px;"><b><?=$fname." ".$mname.' '.$lname?></b></span>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Department</label>
                <div class="field">
                    <span style="line-height: 5px;"><b><?=$edept?></b></span>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Position</label>
                <div class="field">
                    <span style="line-height: 5px;"><b><?=$pos?></b></span>
                </div>
            </div>
            
            <div class="form_row">
                <label class="field_name align_right"><strong>Purpose of Attendance</strong></label>
                <div class="field">
                    <textarea rows="3" style="width: 100%;resize: none;" name="poa" id="poa" placeholder="Purpose of Attendance" readonly=""><?=$purpose?></textarea>
                </div>
            </div>
            
            <div class="form_row">
                <label class="align_right"><h4><strong>PROGRAM DETAILS</strong></h4></label>
            </div>
                        
            <div class="form_row">
                <label class="field_name align_right"><strong>Course Title</strong></label>
                <div class="field no-search">
                    <textarea class="isreq" rows="2" style="width: 100%;resize: none;" name="course" id="course" placeholder="Course Title"><?=$course?></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Date From</strong></label>
                <div class="field">
                    <div class="input-group date" id="datesetfrom" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$dfrom?>" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                    <strong>To</strong>
                    <div class="input-group date" id="datesetto" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="datesetto" type="text" value="<?=$dto?>" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Time Start</strong></label>
                <div class="field">
                    <div class="input-group bootstrap-timepicker">
                        <input class="input-small align_center" type="text" name="tfrom" id="tfrom" value="<?=$tstart?>" style="width: 125px;" readonly="" />
                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                    <strong>To</strong>
                    <div class="input-group bootstrap-timepicker">
                        <input class="input-small align_center" type="text" name="tto" id="tto" value="<?=$tend?>" style="width: 125px;" readonly="" />
                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Days</strong></label>
                <div class="field no-search">
                    <input type="text" name="ndays" id="ndays" value="<?=$days?>" readonly="" />
                </div>
            </div>

			<div class="form_row">            
				<label class="field_name align_right"><strong>Request for University Assistance </strong></label>
				<div class="field">
					<table style="border-spacing: 10px;border-collapse: separate;">
						<tr>
							<th></th>
							<th>Requested</th>
							<th>Approved</th>
						</tr>
						<tr style="display:none">
							<td>Paid Work Day</td>
							<td><input type="text" name="pwd" id="pwd" value="<?=$paid?>" readonly="" /></td>
							<td><input type="text" class="isreq" name="pwdApproved" id="pwdApproved" value="<?=$pwdApproved?>" <?=$isReadonly?>/></td>
						</tr>
						<tr>
							<td>Course Fee</td>
							<td><input type="text" name="cfee" id="cfee" value="<?=$cfee?>" readonly="" /></td>
							<td><input type="text" class="isreq" name="cfeeApproved" id="cfeeApproved" onkeypress="return numbersonly(event,this)" onblur="totalApproved()" value="<?=$cfeeApproved?>" <?=$isReadonly?>/></td>
						</tr>
						<tr>
							<td>Meal</td>
							<td><input type="text" name="meal" id="meal" value="<?=$meal?>" readonly="" /></td>
							<td><input type="text" class="isreq" name="mealApproved" id="mealApproved" onkeypress="return numbersonly(event,this)" onblur="totalApproved()" value="<?=$mealApproved?>" <?=$isReadonly?>/></td>
						</tr>
						<tr>
							<td>Transpodfdfgrtation</td>
							<td><input type="text" name="transpo" id="transpo" value="<?=$transpo?>" readonly="" /></td>
							<td><input type="text" class="isreq" name="transpoApproved" id="transpoApproved" onkeypress="return numbersonly(event,this)" onblur="totalApproved()" value="<?=$transpoApproved?>" <?=$isReadonly?>/></td>
						</tr>
						<tr>
							<td>Hotel</td>
							<td><input type="text" name="hotel" id="hotel" value="<?=$hotel?>" readonly="" /></td>
							<td><input type="text" class="isreq" name="hotelApproved" id="hotelApproved" onkeypress="return numbersonly(event,this)" onblur="totalApproved()" value="<?=$hotelApproved?>" <?=$isReadonly?>/></td>
						</tr>
						<tr>
							<td>Other Miscellaneous Fee</td>
							<td><input type="text" name="othermicellaneous" id="othermicellaneous" value="<?=$othermicellaneous?>" readonly="" /></td>
							<td><input type="text" class="isreq" name="othermiscellaneousApproved" id="othermiscellaneousApproved" onkeypress="return numbersonly(event,this)" onblur="totalApproved()" value="<?=$othermiscellaneousApproved?>" <?=$isReadonly?>/></td>
						</tr>
						<tr>
							<td>Total Cost</td>
							<td>&#8369; <label name="tcLabel" id="tcLabel"/><?=$totalcost?></label></td>
							<input type="hidden" name="tc" id="tc" value="<?=$totalcost?>" readonly="" />
							<td>&#8369; <label name="tcApprovedLabel" id="tcApprovedLabel"/></label></td>
							<input type="hidden" name="tcApproved" id="tcApproved" readonly=""/>
						</tr>
					</table>
				</div>
            </div>
			
            <div class="form_row">
                <label class="field_name align_right"><strong>Venue</strong></label>
                <div class="field">
                    <textarea rows="3" style="width: 100%;resize: none;" name="venue" id="venue" placeholder="Venue" readonly=""><?=$venue?></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Speaker</strong></label>
                <div class="field">
                    <textarea rows="2" style="width: 100%;resize: none;" name="speaker" id="speaker" placeholder="Speaker" readonly=""><?=$speaker?></textarea>
                </div>
            </div>
            <div class="form_row" hidden="">
                <label class="field_name align_right"><strong>Miscellaneous Expenses</strong></label>
                <div class="field">
                    <textarea rows="2" style="width: 100%;resize: none;" name="miscellaneous" id="miscellaneous" placeholder="Miscellaneous Expense" readonly=""><?=$miscellaneous?></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Statement of Commitment</strong></label>
                <div class="field">
                    <textarea rows="3" style="width: 100%;resize: none;" name="soc" id="soc" placeholder="Statement of Commitment" readonly=""><?=$statement?></textarea>
                </div>
            </div>
            <?if($job == "edit"){?>
            <div class="form_row">
                <label class="field_name align_right"><strong>Status</strong></label>
                <div class="field no-search">
                    <select class="form-control" name="status" id="status" <?= (in_array($status,array("APPROVED","DISAPPROVED")) ? $isdisabled : "")?>>
                        <?
                            $opt_status = $this->extras->showLeaveStatus();
                            foreach($opt_status as $c=>$val){
                        ?><option<?=($c==$status ? " selected" : "")?> value="<?=$c?>" ><?= ($val=="PENDING"?"Select status..":$val) ?></option><?    
                        }
                        ?>
                    </select>
                </div>
            </div>
            <?}else{?>
            <div class="form_row">
                <label class="field_name align_right">Status</label>
                <div class="field no-search">
                    <input type="text" value="<?=$status?>" readonly="" />
                </div>
            </div>
            <?}?>
        </div>
    </div>
</div>
<div class="modal-footer">
    <div id="loading" hidden=""></div>
    <div id="saving">
        <?if($job == "edit"){?>
            <button type="button" id="save" class="btn btn-danger">Save</button>
        <?}?>
        <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
    </div>
</div>
<script>
if("<?=in_array($status,array("APPROVED","DISAPPROVED"))?>")
    $("#save").hide();
else
    $("#save").show();

$("#save").click(function(){
	if("<?=$r?>" == "passed")
	{
		if($("#cfeeApproved").val() == "" || $("#mealApproved").val() == "" || $("#transpoApproved").val() == "" || $("#hotelApproved").val() == "" || $("#othermiscellaneousApproved").val() == "")
		{
			alert("Please fill up approved fields!");
		}
		else
		{
			save();
		}
	}
	else
	{
		save();
	}
	
});

function save(){
	$.ajax({
			url:"<?=site_url("employeemod_/loadmodelfunc")?>",
			type:"POST",
			data:{
				id: "<?=$id?>",
				aid: "<?=$aid?>",
				eid: "<?=$eid?>",
				status: $("#status").val(),
				// model: "seminar_approve_head"
				model: "seminar_approve_head_withsequence",
				pwdApproved: $("#pwdApproved").val(),
				coursefeeApproved: $("#cfeeApproved").val(),
				mealApproved: $("#mealApproved").val(),
				transportationApproved: $("#transpoApproved").val(),
				hotelApproved: $("#hotelApproved").val(),
				othermiscellaneousApproved: $("#othermiscellaneousApproved").val(),
				tcApproved: $("#tcApproved").val()
			},
			success: function(msg){
				alert(msg);
				location.reload();
				//console.log(msg);
				//$("#close").click();
				//view_seminar_status();
			}
		});
}

/*
 * Functions
 */
function numbersonly(evt, myfield, e, dec, id)
{ ///< edited for cross-browser compatibility
    var key;
    var keychar;
    var e = evt || window.event;
    if (e)         key = e.which || e.keyCode;
    // else if (window.event)   key = window.event.keyCode;
    // else                return true;
    keychar = String.fromCharCode(key);
        
    // control keys
    if ((key==null) || (key==0) || (key==8) || (key==9) || (key==13) || (key==27) ) return true;
        
    // numbers
    else if (((id ? "0123456789.- " : "0123456789.").indexOf(keychar) > -1))   return true;
        
    // decimal point jump
    else if (dec && (keychar == "."))
    {
        myfield.form.elements[dec].focus();
        return false;
    }
    else    return false;
}

function totalApproved() 
{
	var courseFee = 0;
	var meal = 0;
	var transportation = 0;
	var hotel = 0;
	var miscellaneous = 0;
	if($("#cfeeApproved").val())
	{
		courseFee = $("#cfeeApproved").val();
	}
	if($("#mealApproved").val())
	{
		meal = $("#mealApproved").val();
	}
	if($("#transpoApproved").val())
	{
		transportation = $("#transpoApproved").val();
	}
	if($("#hotelApproved").val())
	{
		hotel = $("#hotelApproved").val();
	}
	if($("#othermiscellaneousApproved").val())
	{
		miscellaneous = $("#othermiscellaneousApproved").val();
	}
	
	$("#tcApproved").val(parseFloat(courseFee) + parseFloat(meal) + parseFloat(transportation) + parseFloat(hotel) + parseFloat(miscellaneous));
	$("#tcApprovedLabel").html(parseFloat(courseFee) + parseFloat(meal) + parseFloat(transportation) + parseFloat(hotel) + parseFloat(miscellaneous));
}

totalApproved();

$('.chosen').chosen();
</script>