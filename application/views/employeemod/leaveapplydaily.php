<?php

/**
 * @author Justin
 * @copyright 2016
 */
$desc = "";
$canedit = false;
$datetoday = "";
$timetoday = "";
$display = "";
$user = $this->session->userdata("username");
$lname = $fname = $mname = $pos = $edept = $edcode = $aid = $leavetype = $other = $othertype = $withpay = $dateapplied = $no_days = $fromdate = $todate = $status = $remarks = $isreadonly = $isdisabled = $ishidden = $tfrom = $tto = $otfrom = $otto = $col = $remarks_approver ="";
if(isset($code) && isset($idnum)){
    $param = "";
    if($this->employee->getClusterHead($this->session->userdata("username")))  
        $tbl = "leave_app_chead"; 
    else if($this->employee->getUnivPhysician($this->session->userdata("username"))){
        $tbl = "leave_app_uphy";
    }
    else if(($this->employee->campus_principal($user)) === true){
         $tbl = "leave_app_principal";
    }else{
        if($dept == "HR"){
			$tbl = "leave_app_hrd";
			$canedit = true;
           
        }else                
            $tbl = "leave_app_dhead";
			$col = ", a.otimefrom, a.otimeto";
    }
    $bfp = $this->employee->getBudgetFinPres($this->session->userdata("username"));
    if($bfp) $tbl = $bfp;


    if(!empty($category))   $param = " AND a.status='$category'";
     $sql = $this->db->query("SELECT a.aid,a.employeeid,a.type,a.other,a.othertype,a.paid,a.timestamp,a.nodays,a.datefrom,a.dateto,a.status,a.dateapproved,a.reason,a.remarks,b.lname,b.fname,b.mname,c.description AS epos, d.description AS edept,d.code, a.timefrom, a.timeto $col
     FROM $tbl a 
     LEFT JOIN employee b ON a.employeeid = b.employeeid
     LEFT JOIN code_position c ON b.positionid = c.positionid
     LEFT JOIN code_office d ON b.deptid = d.code 
     WHERE a.employeeid='{$code}' AND a.id='{$idnum}'  $param");
	 if($sql->num_rows() == 0)
	 {
		$sql = $this->db->query("SELECT a.aid,a.employeeid,a.type,a.other,a.othertype,a.paid,a.timestamp,a.nodays,a.datefrom,a.dateto,a.status,a.dateapproved,a.reason,a.remarks,b.lname,b.fname,b.mname,c.description AS epos, d.description AS edept,d.code, a.timefrom, a.timeto $col
     FROM leave_app_dhead a 
     LEFT JOIN employee b ON a.employeeid = b.employeeid
     LEFT JOIN code_position c ON b.positionid = c.positionid
     LEFT JOIN code_office d ON b.deptid = d.code 
     WHERE a.employeeid='{$code}' AND a.id='{$idnum}'  $param"); 
	 }
     foreach($sql->result() as $row){
        $aid   = $row->aid;
        $empid = $row->employeeid;
        $lname = $row->lname;
        $fname = $row->fname;
        $mname = $row->mname;
        $pos   = $row->epos;
        $edept = $row->edept;
        $aid   = $row->aid;
        $leavetype = $row->type;
        $other = $row->other;
        $othertype = $row->othertype;
        $withpay = $row->paid;
        $dateapplied = date('m/d/Y',strtotime($row->timestamp));
        $no_days = $row->nodays;
		$fromdate = $row->datefrom ? date("Y-m-d",strtotime($row->datefrom)) : $row->datefrom;
		$todate = $row->dateto ? date("Y-m-d",strtotime($row->dateto)) : $row->dateto;
        $status = $row->status;
        $remarks = $row->reason;
        $remarks_approver = $row->remarks;
		$edcode= $row->code;
		
        $tfrom = $row->timefrom;
        $tto = $row->timeto;
		// $otfrom = date("h:i A",strtotime($row->otimefrom)) : date("h:i A",strtotime($row->timefrom));
		// $otto   = date("h:i A",strtotime($row->otimeto)) : date("h:i A",strtotime($row->timeto));
		$otfrom = $row->timefrom;
		$otto   = $row->timeto;
		
    }
    $display = $this->employeemod->offBuss_daytime($code,$fromdate,$todate);
    $isreadonly = "readonly='true'";
    $isdisabled = "disabled";
    $ishidden   = " hidden";
	
	if( $tfrom == "00:00:00"){$tfrom = "";}else{$tfrom = date("H:i:s",strtotime($tfrom));}
	if( $tto == "00:00:00"){$tto = "";}else{$tto = date("H:i:s",strtotime($tto));}
	if( $otfrom == "00:00:00"){$otfrom = "";}else{$otfrom = $tfrom;}
	if( $otto == "00:00:00"){$otto = "";}else{$otto = $tto;}
	
}
?>

<style>
.modal{
    width: 50%;
    left: 0;
    right: 0;
    margin: auto;
}
.tooltip {
  background: #0072C6;
  color: #fff;
  display: block;
  left: -100px;
  opacity: 0;
  margin-top: 15px;
  pointer-events: none;
  position: absolute;
  width: 250px;
  -webkit-transform: translateY(10px);
     -moz-transform: translateY(10px);
      -ms-transform: translateY(10px);
       -o-transform: translateY(10px);
          transform: translateY(10px);
  -webkit-transition: all .25s ease-out;
     -moz-transition: all .25s ease-out;
      -ms-transition: all .25s ease-out;
       -o-transition: all .25s ease-out;
          transition: all .25s ease-out;
  -webkit-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
     -moz-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
      -ms-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
       -o-box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
          box-shadow: 2px 2px 6px rgba(0, 0, 0, 0.28);
}
/* Triangle */
.tooltip:before {
  border-left: solid transparent 10px;
  border-right: solid transparent 10px;
  border-top: solid #1496bb 10px;
  content: " ";
  margin-top: -10px;
  height: 0;
  left: 48.5%;
  margin-left: -13px;
  position: absolute;
  width: 0;
}
#tview:hover .tooltip {
  opacity: 1;
  pointer-events: auto;
  -webkit-transform: translateY(0px);
     -moz-transform: translateY(0px);
      -ms-transform: translateY(0px);
       -o-transform: translateY(0px);
          transform: translateY(0px);
}
#tview .tooltip {   display: none;    }
#tview:hover .tooltip { display: block; }
.th-style{
    background-color: #2e5266;
    color: #ffffff;
    text-align: center;
}
 .swal2-cancel{
   margin-right: 20px;
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
                    <td><strong>Leave Application</strong></td>
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
                        <!-- <input type="checkbox" name="dltype" value="UT/HD"  <?=($othertype == "UT/HD" ? " checked" : "")?> <?=$isdisabled?>/> UNDERTIME/HALFDAY &nbsp;&nbsp;&nbsp; -->
                        <input type="checkbox" name="dltype" value="NO PUNCH IN/OUT" <?=($othertype == "NO PUNCH IN/OUT" ? " checked" : "")?> <?=$isdisabled?>/> NO TIME IN/OUT &nbsp;&nbsp;&nbsp;
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">With Pay?</label>
                    <div class="field no-search">
                        <select class="<?=($isdisabled ? "" : "chosen")?>" name="withpay" id="withpay" <?=$isdisabled?>>
							<option>Select</option>
							<?=$this->employeemod->withPay($withpay);?>
						</select>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Leave From</label>
                    <div class="field">
                        <div class="input-group date" id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$fromdate ? $fromdate : $datetoday?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>

                        <?if($othertype != "NO PUNCH IN/OUT"){?>
                        <div class="input-group date" id="datesetto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                            <input class="align_center" size="16" name="datesetto" type="text" value="<?=$todate ? $todate : $datetoday?>" readonly>
                            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                        </div>
                        <?}?>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Applied Time In</label>
                    <?if($othertype == "NO PUNCH IN/OUT"){?>
                      <div class="field">
                          <table class="table table-hover table-bordered datatable" id="tableId">
                              <thead>
                                  <th class="th-style" style="text-align: center;">Applied Time</th>
                                  <th class="th-style" style="text-align: center;">Approved Time</th>
                                  <?if($canedit){?><th class="th-style" style="text-align: center;"></th><?}?>
                              </thead>
                              <tbody id="tbody-data">
                                  <? 
                                      $timerecord = $this->employeemod->findApplyTimeRecord($aid);
                                      foreach ($timerecord as $row) {
                                          $style = "text-align: center; font-weight: bold;";
                                          if($row->status == "change") $style .= " background-color: #95bbd0;";
                                          if($row->status == "disapproved") $style .= " background-color: #d09298;";
                                  ?>
                                      <tr id="tr-<?=$row->tid?>">
                                          <td id="tapplied-<?=$row->tid?>" style="<?=$style?>"><?=isset($row->timein)?strtoupper(date('h:i: a',strtotime($row->timein))) : "xx:xx xx"?> - <?=isset($row->timeout)?strtoupper(date('h:i: a',strtotime($row->timeout))) : "xx:xx xx"?></td>
                                          <?if($canedit){?>
                                            <td id="tapproved-<?=$row->tid?>" style="<?=$style?>"><?=isset($row->actual_timein)?strtoupper(date('h:i a',strtotime($row->actual_timein))) : date('h:i: A',strtotime($row->timein))?> - <?=isset($row->actual_timeout)?strtoupper(date('h:i: a',strtotime($row->actual_timeout))) : date('h:i A',strtotime($row->timeout))?></td>
                                            <td id="tedit-<?=$row->tid?>" style="<?=$style?>" code='<?=$row->status?>'>
                                              <?if($row->status == "change" && $status == "PENDING"){?>
                                                <div class="btn-group">
                                                  <a class="btn btn-primary" href="#" id="t_edit" code="<?=$row->tid?>"><i class="glyphicon glyphicon-edit"></i></a>
                                                  <a class="btn btn-danger" href="#" id="d_approved" code="<?=$row->tid?>"><i class="glyphicon glyphicon-thumbs-down" id="icon"></i></a>
                                                </div>
                                              <?}?>
                                            </td>
                                          <?}else{?>
                                            <td style="<?=$style?>"><?=isset($row->actual_timein)?strtoupper(date('h:i a',strtotime($row->actual_timein))) : "xx:xx xx"?> - <?=isset($row->actual_timeout)?strtoupper(date('h:i a',strtotime($row->actual_timeout))) : "xx:xx xx"?></td>
                                          <?}?>
                                      </tr>
                                  <?  } // end of foreach
                                  ?>
                              </tbody>
                          </table><br>   
                      </div>
                    <?}else{?>
                    <div class="field">
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" value="<?=$otfrom?>" style="width: 125px;" readonly=""/>
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                        To
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" value="<?=$otto?>" style="width: 125px;" readonly="" />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                    </div>
                    <?}?>
                </div>             
                <div class="form_row">
                    <label class="field_name align_right">Time In</label>
                    <div class="field">
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="tfrom" id="tfrom" value="<?=($tfrom ? $tfrom : $timetoday)?>" style="width: 125px;" <?=$isreadonly?> />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                        To
                        <div class="input-group bootstrap-timepicker">
                            <input class="input-small align_center" type="text" name="tto" id="tto" value="<?=($tto ? $tto : $timetoday)?>" style="width: 125px;" <?=$isreadonly?> />
                            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                        </div>
                        <?if($canedit){?>
                        <input type="hidden" id="oldtfrom" value="<?=$tfrom?>" />
                        <input type="hidden" id="oldtto" value="<?=$tto?>" />
                        <div class="btn-group">
                            <a class="btn btn-primary" href="#" id="tview" title="Attendance">
                                <i class="icon-eye-open"></i>
                                <div class="tooltip">
                                    <div class="tdata">
                                        <table class="table datatable">
                                                <tr style="background: #510051;color: #ADAD0E;">
                                                    <th>Date</th>
                                                    <th>Time In</th>
                                                    <th>Time Out</th>
                                                </tr>
                                                <?
                                                if($othertype == "NO PUNCH IN/OUT") $display = $this->db->query("SELECT * FROM timesheet WHERE userid='0003' AND (timein LIKE '%$fromdate%' OR timeout LIKE '%$fromdate%');");
                                                if($display->num_rows() > 0){
                                                    foreach($display->result() as $data){?>
                                                <tr>
                                                    <td><?=date("F d, Y",strtotime($data->timein))?></td>
                                                    <td><?=date("h:i A",strtotime($data->timein))?></td>
                                                    <td><?=date("h:i A",strtotime($data->timeout))?></td>
                                                </tr>
                                                <?  }
                                                }else{?>
                                                <tr>
                                                    <td colspan="3">No Data Available..</td>
                                                </tr>
                                                <?}?>
                                        </table>
                                        
                                    </div>                           
                                </div>
                            </a>
                            <?if($othertype != "NO PUNCH IN/OUT" ){?>
                              <a class="btn btn-primary" href="#" id="tedit"><i class="glyphicon glyphicon-edit"></i></a>
                              <a class="btn btn-primary" href="#" id="tsave"><i class="icon-save"></i></a>
                            <?}else{?>
                              <?if($status == "PENDING"){?>
                              <a class="btn btn-primary" href="#" id="t_save" code="add"><i class="icon-save"></i></a>
                              <a class="btn btn-primary" href="#" id="t_remove"><i class="glyphicon glyphicon-remove-sign"></i></a>
                              <?}?>
                            <?}?>    
                        </div>
                        <?}?>
                    </div>
                </div>
                <div class="form_row" id="hideDays">
                    <label class="field_name align_right">Days</label>
                    <div class="field no-search">
                        <input type="text" name="ndays" id="ndays" value="1" <?=$isreadonly?> />
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Reason</label>
                    <div class="field no-search">
                        <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason" <?=$isreadonly?>><?=$remarks?></textarea>
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
                                    ?><option<?=($c==$status ? " selected" : "")?> value="<?=$c?>" ><?=($val=="PENDING"?"Select status..":$val)?></option><?    
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                </div>
                <!-- remarks -->
                <div class="form_row">
                    <label class="field_name align_right">Remarks</label>
                    <div class="field">
                        <input type="text" name="remarks" id="remarks" value="<?=$remarks_approver?>" style="width: 100%;resize: none;" <?=($status == "PENDING") ? "" : "disabled='true'";?>/>
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
    $("input[name='dltype']").on('change', function() {
        $("input[name='dltype']").not(this).prop('checked', false);
    });
    $("input[name='datesetfrom']").change(function(){
       var  start = new Date($(this).val()),
            end   = new Date($("input[name='datesetto']").val()),
            diff  = new Date(end - start),
            days  = diff/1000/60/60/24;
            if(days >= 0)   days += 1;
            $("#ndays").val(days);
    });
    $("input[name='datesetto']").change(function(){
       var  start = new Date($(this).val()),
            end   = new Date($("input[name='datesetfrom']").val()),
            diff  = new Date(start - end),
            days  = diff/1000/60/60/24;
            if(days >= 0)   days += 1;
            $("#ndays").val(days);
    });
    $("#save").click(function(){
        var form_data   =   $("#frmleave").serialize();
        if($("input[name='dltype']").is(":checked") == false){
            alert("Daily Leave Type is required!.");
            return false;
        }else if($("#reason").val() == ""){
            $("#reason").css("border-color","red").attr("placeholder", "This field is required!.").focus();
            return false;
        }else{
            $("#saving").hide();
            $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
            $.ajax({
               url      :   "<?=site_url("employeemod_/loadmodelfunc")?>",
               type     :   "POST",
               data     :   form_data,
               success  :   function(msg){
                alert(msg);
                loadleavemod();
                loadleavehistory();
                $("#close").click();
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
}
$(".chosen").chosen();
</script>

<?if(isset($code)){?>
<script>
    $("#hideDays").hide();
    $(document).ready(function(){ 
        if("<?=in_array($status,array("APPROVED","DISAPPROVED"))?>"){
            $("#button_save_modal").hide();
        }else{
            $("#button_save_modal").show();
        }
    });

    // new function and event added for #ica-hyperion 21090
    // author : justin (with e)
    <?if($othertype == "NO PUNCH IN/OUT"){?>
      function clearTime(){
        $("input[name='tfrom'],input[name='tto']").val('');
        $("#t_save").attr('code','add');
      }

      $("input[name='tfrom'],input[name='tto']").attr("readonly",false).timepicker({
          minuteStep: 1,
          showSeconds: false,
          showMeridian: true,
          defaultTime: false
      });

      function changeBackColor(tid,job,code){
        var bcolor = ""; // approved
        if(job == 1) 
          bccolor = "#d09298"; // disapproved
        else
          bccolor = "#95bbd0"; // back to edit


        var style = "text-align: center; font-weight: bold; background-color:" + bccolor + ";";
        
        $("#tapplied-"+ tid +", #tapproved-"+ tid+", #tedit-"+ tid).removeAttr('style');
        $("#tapplied-"+ tid +", #tapproved-"+ tid+", #tedit-"+ tid).attr('style',style);
        $("#tedit-"+ tid).attr('code',code);
      }

      function validateTime(tfrom, tto){
        
        if(tfrom == "" || tto == "") return false;
        
        tfrom = convertTimeToNumber(tfrom);
        tto = convertTimeToNumber(tto);

        if(tfrom > tto || tfrom == tto) return false;

        return true;
      }

    function reloadBTN(){
      $("#t_edit, #d_approved, #t_save, #t_remove, #t_del").unbind('click').click(function(){
          
          // disapproved btn
          if($(this).attr('id') == "d_approved"){
            var job = 0, code = "change";

            if($("#icon").attr('class') == 'glyphicon glyphicon-thumbs-down'){
              if($(this).attr('code') == $("#t_save").attr('code')){
                alert('This timerecord is currently being modified.');
                return;
              }

              // disapproved time
              $("#icon", this).removeAttr('class');
              $("#icon").attr('class','glyphicon glyphicon-thumbs-up');
              $(this).attr('class','btn blue');
              $("#tapproved-"+ $(this).attr('code')).html($("#tapplied-"+ $(this).attr('code')).html())
              job = 1;
              code = "disapproved";
            }else{
              // back to able edit
              $("#icon", this).removeAttr('class');
              $("#icon").attr('class','glyphicon glyphicon-thumbs-down');
              $(this).attr('class','btn btn-danger');
            }

            changeBackColor($(this).attr('code'), job, code);
            return;
          }

          // remove btn
          if($(this).attr('id') == "t_remove"){
            clearTime();
            return;
          }

          // edit btn
          if($(this).attr('id') == "t_edit"){
            if($("#tedit-"+ $(this).attr('code')).attr('code') == 'disapproved'){
              alert("Error: Unable to edit this applied time. You must to approve the applied time before to edit.");
              return;
            }

            var atime = $("#tapproved-"+ $(this).attr('code')).html().split(' - ');
            $("input[name='tfrom']").val(atime[0]);
            $("input[name='tto']").val(atime[1]);

            $("#t_save").attr('code',$(this).attr('code')); // change the code value of add
            return;
          }

          // save or add
          if($(this).attr('id') == "t_save"){

            // validate the time first
            if(validateTime($("input[name='tfrom']").val(), $("input[name='tto']").val()) === false){
              alert("Invalid Time In/Out.");
              return;
            }

            if($(this).attr('code') == "add"){
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
                    tbody_data += "<td style=\"text-align: center;\" id=\"tapplied-"+ newCode +"\">xx:xx xx - xx:xx xx</td>"; // time in 
                    tbody_data += "<td style=\"text-align: center;\" id=\"tapproved-"+ newCode +"\">"+ $("input[name='tfrom']").val() +" - "+ $("input[name='tto']").val() +"</td>"; // time out
                    tbody_data += "<td style=\"text-align: center;\" code='change' id=\"tedit-"+ newCode +"\">"; 
                    tbody_data += "<a class=\"btn blue\" code=\""+ newCode +"\" id=\"t_edit\"><i class=\"glyphicon glyphicon-edit\"></i></a>"; // edit
                    tbody_data += "<a class=\"btn btn-danger\" code=\""+ newCode +"\" id=\"t_del\"><i class=\"glyphicon glyphicon-remove-sign\"></i></a>"; // remove
                    tbody_data += "</td>"; 
                    tbody_data += "</tr>";
                    
                    $("#tbody-data").html($("#tbody-data").html() +""+ tbody_data);
                    changeBackColor(newCode, 0, 'change');
                    clearTime();
                    reloadBTN();
            }else{
                    // save timerecord
                    $("#tapproved-"+$(this).attr('code')).html($("input[name='tfrom']").val() +" - "+ $("input[name='tto']").val());
                    clearTime();
            }
            return;
          }

          // del
          if($(this).attr('id') == "t_del"){
            var res = confirm("Are you sure, you want to delete this timerecord ('"+ $('#tapproved-'+ $(this).attr('code')).html() +"')?");
            if(res){
              $('#tr-'+ $(this).attr('code')).remove();
              reloadBTN();
            }
            return;
          }
      });
    }
    reloadBTN();
    <?}?>  
    // end of new function added for #ica-hyperion 21090

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
                remarks : $("#remarks").val(),
                model: "leave_approve_head",
                ltype: "<?=$leavetype?>",
                dept: "<?=$dept?>",
                eddept: "<?=$edcode?>",
                dltype: "<?=$othertype?>",
                othleave: "<?=$other?>",
            },
            success: function(msg){
                $("#loading").remove();
                $(".grey,#button_save_modal").show();
                $("#modalclose").click();
                <?if($canedit && $othertype == "NO PUNCH IN/OUT"){?>
                  // save time record
                  if($("#mh_status").val() == "APPROVED"){
                      var timeRecord = "";
                      $("#tableId").find("tbody tr").each(function(){
                        if($(this).find("td").length > 1){
                            timeRecord += (timeRecord?"|":"");
                            var tID = $(this).attr('id');
                            tID = tID.split("tr-");
                            timeRecord += tID[1]; // timesheet id or new add id
                            //timeRecord += "~u~";
                            //timeRecord += $(this).find('td:eq(0)').text(); // time in
                            timeRecord += "~u~";
                            timeRecord += $(this).find('td:eq(1)').text(); // approved timein
                            timeRecord += "~u~";
                            timeRecord += $(this).find('td:eq(2)').attr('code');
                                                
                        } // end of if condition
                      }); // end of tableid

                      $.ajax({
                        url     : "<?=site_url("employeemod_/saveApprovedTimeRecord")?>",
                        type    : "POST",
                        data    : {
                                    aid        : "<?=$aid?>",
                                    cdate      : "<?=$fromdate?>",
                                    timerecord : timeRecord
                                  },
                        success : function(res){ }
                      });

                  }

                <?}?>
                alert(msg);
                location.reload();  
            }
        });

    });
</script>
<?}?>

<?if($canedit){?>
<script>
$("#tview").click(function(){
    
});
$("#tedit").click(function(){
    $("#tfrom").focus();
    $("input[name='tfrom'],input[name='tto']").attr("readonly",false).timepicker({
        minuteStep: 1,
        showSeconds: false,
        showMeridian: true,
        defaultTime: false
    });
});
$("#tsave").click(function(){
    var form_data = {
                        eid     : "<?=$code?>",
                        aid     : "<?=$aid?>",
                        model   : "modify_offbus_time",
                        oldtfrom: $("#oldtfrom").val(),
                        oldtto  : $("#oldtto").val(),
                        newtfrom: $("#tfrom").val(),
                        newtto  : $("#tto").val()
                    };
    $.ajax({
            url:"<?=site_url("employeemod_/loadmodelfunc")?>",
            type:"POST",
            data:form_data,
            success: function(msg){
                alert(msg); 
                $("input[name='tfrom'],input[name='tto']").attr("readonly",true).timepicker("remove");
            }
        });
});
</script>
<?}?>
<script type="text/javascript" src="<?=base_url()?>/js/timeValidation.js"></script>