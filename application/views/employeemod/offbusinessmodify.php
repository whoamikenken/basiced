<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
if($this->input->post("id")){
    $query = $this->employeemod->modifyOffbus_query($this->input->post("id"));
    $aid = $this->input->post("id");
    $othertype  = $query->row()->othertype;
    $paid   = $query->row()->paid;
    $dfrom  = $query->row()->datefrom;
    $dto    = $query->row()->dateto;
    $tfrom  = date("h:i A",strtotime($query->row()->timefrom));;
    $tto    = date("h:i A",strtotime($query->row()->timeto));
    if($othertype == "NO PUNCH IN/OUT"){
        $tfrom = "";
        $tto = "";
    }
    $nodays = $query->row()->nodays;
    $reason = $query->row()->reason;
}else exit();
 

$lblDate = array(
                    "ABSENT" => "Date of Absent",
                    "DIRECT" => "OB Date",
                    "NO PUNCH IN/OUT" => "Date of Deficiency"
                );
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
<input name="model" value="modifyOffBus" hidden=""/>
<input name="ltype" value="other" hidden=""/>
<input name="othleave" value="DA" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="10%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>Leave Application</strong></td>
                </tr>
            </table>
        </div>
        <div class="modal-body">
            <div class="content">
                <div class="form_row">
                    <div class="field no-search">
                        <input type="checkbox" name="dltype" value="ABSENT" <?=($othertype == "ABSENT" ? " checked" : "")?> /> ABSENT &nbsp;&nbsp;&nbsp;
                        <input type="checkbox" name="dltype" value="DIRECT" <?=($othertype == "DIRECT" ? " checked" : "")?> /> OB &nbsp;&nbsp;&nbsp;
                        <!-- comment by justin (with e) for #ica-21090 -->
                        <!-- <input type="checkbox" name="dltype" value="UT/HD"  <?=($othertype == "UT/HD" ? " checked" : "")?> /> UNDERTIME/HALFDAY &nbsp;&nbsp;&nbsp; --> 
                        <!-- updated by justin (with e) for #ica-21090 -->
                        <input type="checkbox" name="dltype" value="NO PUNCH IN/OUT" <?=($othertype == "NO PUNCH IN/OUT" ? " checked" : "")?> /> CORRECTION OF TIME IN/OUT &nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                <br>
                <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field">
                    <select class="form-control" name="withpay" id="withpay" >
                        <option>Select</option>
                        <?=$this->employeemod->withPay($paid);?>
                    </select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right" id="lblFrom"><?=$lblDate[$othertype]?></label>
                    <div class="field">
                        <div class="input-group date" id="datesetfrom" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$dfrom?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <?if($othertype != "NO PUNCH IN/OUT"){?>
                        <div class="input-group" id="hideTo">
                            <label class="align_center">To</label>
                        </div>
                        <div class="input-group date hidemo" id="datesetto" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetto" type="text" value="<?=$dto?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <?}?>
                    </div>
                </div>
                <!-- newly addded by justin (with e) for #ica-21090 -->
                <?
                    if($othertype == "NO PUNCH IN/OUT"){
                ?>
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
                                <? 
                                        $timerecord = $this->employeemod->findApplyTimeRecord($this->input->post("id"));
                                        foreach ($timerecord as $row) {
                                            $style = "text-align: center; font-weight: bold;";
                                            if($row->status == "change") $style .= " background-color: #95bbd0;";
                                    ?>
                                        <tr id="tr-<?=$row->tid?>">
                                            <td style="<?=$style?>" id="ti-<?=$row->tid?>"><?=strtoupper(date('h:i a', strtotime($row->timein)))?></td>
                                            <td style="<?=$style?>" id="to-<?=$row->tid?>"><?=strtoupper(date('h:i a', strtotime($row->timeout)))?></td>
                                            <td style="<?=$style?>" code='<?=$row->status?>'>
                                                <?
                                                    if($row->status == "change"){
                                                ?>
                                                    <a class="btn btn-primary" code="<?=$row->tid?>" id="edit"><i class="glyphicon glyphicon-edit"></i></a>
                                                    <a class="btn btn-primary" code="<?=$row->tid?>" id="remove"><i class="glyphicon glyphicon-remove-sign"></i></a>
                                                <?
                                                    }// end if
                                                ?>
                                            </td>
                                        </tr>
                                    <?  } // end of foreach
                                    ?>
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <?  }?>
                <!-- end newly addded by justin (with e) for #ica-21090 -->
                <div class="form_row" id="hideTITO" <?=$othertype == "ABSENT"? 'hidden="true"' : ""?>>
                    <label class="field_name align_right">Time In</label>
                    <div class="field">
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="tfrom" id="tfrom" value="<?=$tfrom?>" style="width: 125px;"/>
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                        To
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="tto" id="tto" value="<?=$tto?>" style="width: 125px;"/>
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                        <div class="input-group" id="hideBtn" <?=$othertype == "DIRECT"? 'hidden="true"' : ""?>>
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
                        <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason"><?=$reason?></textarea>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
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
    var selDLtype = "<?=$othertype?>";
    <?if($othertype != "NO PUNCH IN/OUT"){?>
        $("#hideTable").hide();
        $("#hideTo").hide();
    <?}?>
    $("#hideDays").hide();
    <?if($othertype == "DIRECT"){?>
        $("#hideBtn").hide();
    <?}?>

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

    $("#save").click(function(){
            var form_data   =   $("#frmleave").serialize();
            var tfrom = convertTimeToNumber($("input[name='tfrom']").val());
            var tto = convertTimeToNumber($("input[name='tto']").val());
            var form_data   =   $("#frmleave").serialize();
            if($("input[name='dltype']").is(":checked") == false){
                alert("Daily Leave Type is required!.");
                return false;
            }
            else if($("#withpay").val() == "Select"){
                alert("PLEASE SELECT WITH PAY!.");
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
            else if($("#tableId").find("tbody").find("td").length <= 1 && selDLtype == "NO PUNCH IN/OUT"){
                alert("Time Record is required!.");
                return false;
            }
            else if($("#reason").val() == ""){
                $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
                return false;
            }
            else{
                $("#saving").hide();
                $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
                if(selDLtype == "NO PUNCH IN/OUT")form_data +="&datesetto="+ $("input[name='datesetfrom']").val();
                $.ajax({
                   url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
                   type     :   "POST",
                   data     :   form_data,
                   success  :   function(msg){
                        if(selDLtype == "NO PUNCH IN/OUT" && msg == "Successfully Saved!"){
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
                                            aid         : "<?=$aid?>",
                                            cdate       : $("input[name='datesetfrom']").val(),
                                            timerecord  : timeRecord
                                          },
                                success : function(res){
                                    alert("Successfully Saved!");
                                    loadbushistory();
                                    $("#close").click();
                                }

                            });
                        }else{
                            alert(msg);
                            console.log(msg);
                            loadbushistory();
                            $("#close").click();
                        }
                   }
                });
            }
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
$(".chosen").chosen();
</script>
<script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>