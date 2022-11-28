<?php

/**
 * @author Justin
 * @copyright 2016
 */

$desc = "";
$datetoday = "";
$timetoday = "";
$lname = $fname = $mname = $pos = $edept = $aid = $leavetype = $other = $othertype = $dateapplied = $no_days = $fromdate = $todate = $status = $remarks = $isreadonly = $isdisabled = $ishidden = "";
if(isset($code) && isset($idnum)){
    $param = "";
    if($this->employee->getClusterHead($this->session->userdata("username")))  
        $tbl = "leave_app_chead"; 
    else if($this->employee->getUnivPhysician($this->session->userdata("username"))){
        $tbl = "leave_app_uphy";
    }else{
        if($dept == "HR")   $tbl = "leave_app_hrd";
        else                $tbl = "leave_app_dhead";
    }
    if(!empty($category))   $param = " AND a.status='$category'";
     $sql = $this->db->query("SELECT a.aid,a.employeeid,a.type,a.other,a.othertype,a.timestamp,a.nodays,a.datefrom,a.dateto,a.status,a.dateapproved,a.reason,b.lname,b.fname,b.mname,c.description AS epos, d.description AS edept 
     FROM $tbl a 
     LEFT JOIN employee b ON a.employeeid = b.employeeid
     LEFT JOIN code_position c ON b.positionid = c.positionid
     LEFT JOIN code_office d ON b.deptid = d.code 
     WHERE a.employeeid='{$code}' AND a.id='{$idnum}'  $param");
     foreach($sql->result() as $row){
        $lname = $row->lname;
        $fname = $row->fname;
        $mname = $row->mname;
        $pos   = $row->epos;
        $edept = $row->edept;
        $aid   = $row->aid;
        $leavetype = $row->type;
        $other = $row->other;
        $othertype = $row->othertype;
        $dateapplied = date('m/d/Y',strtotime($row->timestamp));
        $no_days = $row->nodays;
        $fromdate = $row->datefrom;
        $todate = $row->dateto;
        $status = $row->status;
        $remarks = $row->reason;
     }
    $isreadonly = "readonly='true'";
    $isdisabled = "disabled";
    $ishidden   = " hidden";
}
 
 
?>
<style>
.modal{
    width: 700px;
    left: 0;
    right: 0;
    margin: auto;
}
.th-style{
    background-color: #2e5266;
    color: #ffffff;
    text-align: center;
}
</style>
<form id="frmleave">
<!-- <input name="model" value="applyLeave" hidden=""/> -->
<input name="model" value="applyLeaveWithSequence" hidden=""/>
<input name="ltype" value="other" hidden=""/>
<input name="othleave" value="DA" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header" <?=$ishidden?>>
            <table width="100%">
                <tr>
                    <td rowspan="2" width="10%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>OB/Exuse Slip</strong></td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            <div class="content">
                <?if(isset($code)){?>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Name</label>
                    <div class="field">
                        <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$fname." ".$mname.' '.$lname?></b></span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Department</label>
                    <div class="field">
                        <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$edept?></b></span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right" style="line-height: 5px;">Position</label>
                    <div class="field">
                        <span class="field_name" style="line-height: 10px;width: 100%;"><b><?=$pos?></b></span>
                    </div>
                </div>
                <?}?>
                <div class="form_row">
                    <div class="field no-search">
                        <input type="checkbox" name="dltype" value="ABSENT" <?=($othertype == "ABSENT" ? " checked" : "")?> <?=$isdisabled?>/> ABSENT &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="dltype" value="DIRECT" <?=($othertype == "DIRECT" ? " checked" : "")?> <?=$isdisabled?>/> OB &nbsp;&nbsp;&nbsp;
                        <!-- comment by justin (with e) for #ica-21090 -->
                        <!-- <input type="checkbox" name="dltype" value="UT/HD"  <?=($othertype == "UT/HD" ? " checked" : "")?> <?=$isdisabled?>/> UNDERTIME/HALFDAY &nbsp;&nbsp;&nbsp; --> 
                        <!-- updated by justin (with e) for #ica-21090 -->
                        <input type="checkbox" name="dltype" value="NO PUNCH IN/OUT" <?=($othertype == "NO PUNCH IN/OUT" ? " checked" : "")?> <?=$isdisabled?>/> CORRECTION OF TIME IN/OUT &nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                <br>
                <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field">
                    <select class="<?=($isdisabled ? "" : "chosen")?>" name="withpay" id="withpay" <?=$isdisabled?>>
						<option>Select</option>
						<?=$this->employeemod->withPay();?>
					</select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right" id="lblFrom">Leave From</label>
                    <div class="field">
                        <div class="input-group date" id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$datetoday?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <div class="input-group" id="hideTo">
                            <label class="align_center">To</label>
                        </div>
                        <div class="input-group date hidemo" id="datesetto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetto" type="text" value="<?=$datetoday?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                    </div>
                </div>
                <!-- newly addded by justin (with e) for #ica-21090 -->
                <div class="form_row" id="hideTable">
                    <label class="field_name align_right">My Time Record</label>
                    <div class="field">
                        <table class="table table-hover table-bordered datatable" id="tableId">
                            <thead>
                                <th class="th-style" style="text-align: center;">TIME IN</th>
                                <th class="th-style" style="text-align: center;">TIME OUT</th>
                                <th class="th-style" style="text-align: center;">EDIT</th>
                            </thead>
                            <tbody id="tbody-data">
                                <!-- displayed data here -->
                                <tr>
                                <td colspan="3" style="text-align: center;">No Data Available..</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <!-- end newly addded by justin (with e) for #ica-21090 -->
                <div class="form_row" id="hideTITO">
                    <label class="field_name align_right">Time In</label>
                    <div class="field">
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="tfrom" id="tfrom" value="<?=$timetoday?>" style="width: 125px;" <?=$isreadonly?> />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                        To
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="tto" id="tto" value="<?=$timetoday?>" style="width: 125px;" <?=$isreadonly?> />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                        <div class="input-group" id="hideBtn">
                            <a class="btn btn-primary" code='add' id='add' onclick="function"><i class="icon-save"></i></a>
                        </div>
                    </div>
                </div>
                <div class="form_row" id="hideDays">
                    <label class="field_name align_right">Days</label>
                    <div class="field no-search">
                        <input class="col-md-3" type="text" name="ndays" id="ndays" value="1" readonly="" />
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason" <?=$isreadonly?>></textarea>
                    </div>
                </div>
                <?if(isset($code)){?>
                <div class="form_row">
                    <label class="field_name align_right">Status</label>
                        <div class="field">
                            <div class="col-md-4 no-search">
                                <select class="form-control" name="mh_status" id="mh_status" <?= (in_array($status,array("APPROVED","DISAPPROVED")) ? $isdisabled : "")?>>
                                    <?
                                        $opt_status = $this->extras->showLeaveStatus();
                                        foreach($opt_status as $c=>$val){
                                    ?><option<?=($c==$status ? " selected" : "")?> value="<?=$c?>" ><?=$val?></option><?    
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                </div>
                <?}?>
            </div>
        </div>
        <div class="modal-footer" <?=$ishidden?>>
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="save" class="btn btn-danger">Save</button>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>

if("<?=!$isdisabled?>"){
    var selDLtype = "";
    $("#hideTable").hide();
    $("#hideDays").hide();
    $("#hideBtn").hide();

    $("input[name='dltype']").on('change', function() {
        $("input[name='dltype']").not(this).prop('checked', false);

        $("#hideTo").show();
        $(".hidemo").show();
        $("#hideTITO").show();
        $("#hideTable").hide();
        $("#hideBtn").hide();

        // new condition added for #ica-21090 by justin (with e)
        if($(this).val() == "ABSENT"){
            $("#lblFrom").text("Date of Absent");
            $("#hideTITO").hide();
            $("#withpay option:eq(2)").prop("selected", true);
        }
        else if($(this).val() == "DIRECT"){
            $("#lblFrom").text("OB Date");
        }
        else{
            $("#hideBtn").show();
            $("#hideTo").hide();
            $(".hidemo").hide();
            $("#lblFrom").text("Date of Deficiency");
            $("#hideTable").show();
        }
        selDLtype = $(this).val();
        // end of new condition for #ica-21090 by justin (with e)
    });
    $("input[name='datesetfrom']").change(function(){
       var  start = new Date($(this).val()),
            end   = new Date($("input[name='datesetto']").val()),
            diff  = new Date(end - start),
            days  = diff/1000/60/60/24;
            if(days >= 0)   days += 1;
            $("#ndays").val(days);
        // new condition added for #ica-21090 by justin (with e)
        if(selDLtype == "NO PUNCH IN/OUT"){
            // remove all row here
            removeAllrow();

            // displayed loading
            $("#tbody-data").html("<tr><td colspan=\"3\" style=\"text-align: center;\"><img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..</td></tr>");

            // find timerecord here
            $.ajax({
                url     : "<?=site_url("employeemod_/showTimeRecord")?>",
                type    : "POST",
                data    : { cdate : $(this).val() },
                success : function(msg){
                    removeAllrow();
                    $("#tbody-data").html(msg);
                    reloadBtnEvent();
                    $("#add").attr('code','add');
                }
            });
        }
        // end of new condition for #ica-21090 by justin (with e)
    });
    // newly added for #ica-21090 by justin (with e)
    reloadBtnEvent(); // load button event
    function removeAllrow(){
        $( "#tableId tbody tr" ).each( function(){
            this.parentNode.removeChild( this ); 
        });
    }
    function clearTime(){
        $("input[name='tfrom']").val('');
        $("input[name='tto']").val('');
    }
    function reloadBtnEvent(){
        $('#add, #remove, #edit').unbind('click').click(function(){

            // move to edit
            if($(this).attr('id') == 'edit'){
                $("input[name='tfrom']").val($("#ti-"+ $(this).attr('code')).html());
                $("input[name='tto']").val($("#to-"+ $(this).attr('code')).html());
                $("#add").attr('code',$(this).attr('code'));
                return;
            }

            // add new
            if($(this).attr('id') == 'add'){
                // check time
                var tfrom = convertTimeToNumber($("input[name='tfrom']").val());
                var tto = convertTimeToNumber($("input[name='tto']").val());
                if(tfrom > tto || tfrom == tto || $("input[name='tfrom']").val() == "" || $("input[name='tto']").val() == ""){
                    alert("Invalid Time In/Out!.");
                    return;
                }

                if($(this).attr('code') == 'add'){
                    // add new timerecord
                    var newCode = $("#tableId").find("tbody").find("tr").length;
                    if($("#tableId").find("tbody").find("td").length == 1){
                        newCode = "add-0";
                        removeAllrow();
                    }else{
                        newCode = "add-"+ newCode;
                    }
                    var tbody_data = "";
                
                    tbody_data += "<tr id=\"tr-"+ newCode +"\">";
                    tbody_data += "<td style=\"text-align: center;\" id=\"ti-"+ newCode +"\">"+ $("input[name='tfrom']").val() +"</td>"; // time in 
                    tbody_data += "<td style=\"text-align: center;\" id=\"to-"+ newCode +"\">"+ $("input[name='tto']").val() +"</td>"; // time out
                    tbody_data += "<td style=\"text-align: center;\" code='change'>"; 
                    tbody_data += "<a class=\"btn blue\" code=\""+ newCode +"\" id=\"edit\"><i class=\"glyphicon glyphicon-edit\"></i></a>"; // edit
                    tbody_data += "<a class=\"btn blue\" code=\""+ newCode +"\" id=\"remove\"><i class=\"glyphicon glyphicon-remove-sign\"></i></a>"; // remove
                    tbody_data += "</td>"; 
                    tbody_data += "</tr>";
                    
                    $("#tbody-data").html($("#tbody-data").html() +""+ tbody_data);
                    $("#add").attr('code','add');
                    clearTime();
                    reloadBtnEvent();
                    return;
                }else{
                    // save edited timerecord
                    //alert("hellor");
                    $("#ti-"+ $(this).attr('code')).html($("input[name='tfrom']").val());
                    $("#to-"+ $(this).attr('code')).html($("input[name='tto']").val());
                    $("#add").attr('code','add');
                    clearTime();
                    reloadBtnEvent();
                    return;
                }
            }

            // remove row
            if($(this).attr('id') == 'remove'){
                var res = confirm("Are you sure, you want to remove this time record?");
                if(res){
                    $("#tr-"+ $(this).attr('code')).remove();
                    if($("#tableId").find("tbody").find("td").length == 0) $("#tbody-data").html("<tr><td colspan=\"3\" style=\"text-align: center;\">No Data Available..</td></tr>");
                }
            }
        });

    }
    // end of newly added for #ica-21090 by justin (with e)

    $("input[name='datesetto']").change(function(){
       var  start = new Date($(this).val()),
            end   = new Date($("input[name='datesetfrom']").val()),
            diff  = new Date(start - end),
            days  = diff/1000/60/60/24;
            if(days >= 0)   days += 1;
            $("#ndays").val(days);
    });
    $("#save").unbind('click').click(function(){
        // updated by justin (with e) for #ica-21090 
		if($("#withpay").val() != "Select")
		{
            var tfrom = convertTimeToNumber($("input[name='tfrom']").val());
            var tto = convertTimeToNumber($("input[name='tto']").val());
            var form_data   =   $("#frmleave").serialize();
            if($("input[name='dltype']").is(":checked") == false){
                alert("Daily Leave Type is required!.");
                return false;
            }
            else if($("input[name='datesetfrom']").val() == "" && $("input[name='datesetto']").val() =="" && selDLtype != "NO PUNCH IN/OUT"){
                alert("Date From/To is required!.");
                return false;
            }
            else if($("input[name='datesetfrom']").val() == "" && selDLtype == "NO PUNCH IN/OUT"){
                alert("Date of Deficiency is required!.");
                return false;
            }
            else if(($("input[name='tfrom']").val() == "" || $("input[name='tto']").val() =="") && selDLtype == "DIRECT"){
                alert("Time In/Out is required!.");
                return false;
            }
            else if((tfrom > tto || tfrom == tto)  && selDLtype == "DIRECT"){
                alert("Invalid Time In/Out!.");
                return false;
            }
            else if($("input[name='tfrom']").val() != "" && $("input[name='tto']").val() !="" && selDLtype == "NO PUNCH IN/OUT"){
                alert("Please saved the Time In/Out first!.");
                return false;
            }
            else if($("#tableId").find("tbody").find("td").length == 1 && selDLtype == "NO PUNCH IN/OUT"){
                alert("Time Record is required!.");
                return false;
            }
            else if($("#reason").val() == ""){
                $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
                return false;
            }
            else{
                //alert("save na");
				$("#saving").hide();
				$("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
				$.ajax({
				   url      :   "<?=site_url("employeemod_/saveOffBusinessApply")?>",
				   type     :   "POST",
				   dataType :   'json',
				   data     :   form_data,
				   success  :   function(msg){
					
                        if(selDLtype == "NO PUNCH IN/OUT" && msg.msg == "Application Sent!."){
                            // get the timerecord on table
                            var timeRecord = "";
                            $("#tableId").find("tbody tr").each(function(){
                                if($(this).find("td").length > 1){
                                    //if($(this).find('td:eq(2)').attr('code') == "change"){
                                        timeRecord += (timeRecord?"|":"");
                                        var tID = $(this).attr('id');
                                        tID = tID.split("tr-");
                                        timeRecord += tID[1]; // timesheet id or new add id
                                        timeRecord += "~u~";
                                        timeRecord += $(this).find('td:eq(0)').text(); // time in
                                        timeRecord += "~u~";
                                        timeRecord += $(this).find('td:eq(1)').text(); // time out
                                        timeRecord += "~u~";
                                        timeRecord += $(this).find('td:eq(2)').attr('code');
                                    //}
                                }
                            });

                            // save here the timerecord
                            $.ajax({
                                url     : "<?=site_url("employeemod_/saveTimeRecord")?>",
                                type    : "POST",
                                data    : {
                                            aid         : msg.base_id,
                                            cdate       : $("input[name='datesetfrom']").val(),
                                            timerecord  : timeRecord
                                          },
                                success : function(res){
                                    alert(res);
                                    loadbushistory();
                                    $("#close").click();
                                }

                            });
                        }else{
                            alert(msg.msg);
                            // msg.base_id;
                            loadbushistory();
                            $("#close").click();
                        }
                   }
                });
			}
		}
		else
		{
			alert("PLEASE SELECT WITH PAY!");
		}
        // end of updated by justin (with e) for #ica-21090 
    });
    $("#datesetfrom,#datesetto").datepicker({
        autoclose: true,
        todayBtn : true
    });
    $("input[name='tfrom'],input[name='tto']").timepicker({
        minuteStep: 1,
        showSeconds: false,
        showMeridian: true,
        defaultTime: false
    });
}
$(".chosen").chosen();
</script>

<?if(isset($code)){?>
<script>
    $(document).ready(function(){ 
        if("<?=in_array($status,array("APPROVED","DISAPPROVED"))?>"){
            $("#button_save_modal").hide();
        }else{
            $("#button_save_modal").show();
        }
    });
            
    $("#button_save_modal").unbind("click").click(function(){  
        $(".modal-footer").append("<div id='loading'><img class='pull-right' src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..</div>");
        $(".grey,#button_save_modal").hide();
        $.ajax({
            url:"<?=site_url("employeemod_/loadmodelfunc")?>",
            type:"POST",
            data:{
                eid: "<?=$code?>",
                id: "<?=$idnum?>",
                aid: "<?=$aid?>",
                status: $("#mh_status").val(),
                model: "leave_approve_head",
                ltype: "<?=$leavetype?>",
                dept: "<?=$dept?>"
            },
            success: function(msg){
                $("#loading").remove();
                $(".grey,#button_save_modal").show();
                $("#modalclose").click();
                alert(msg);
                location.reload();  
            }
        });
    });
</script>
<?}?>
<script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>