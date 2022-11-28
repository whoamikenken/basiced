
<?php
/**
 * @author Justin
 * @copyright 2016
 */
$CI =& get_instance();
$CI->load->model('utils');

$lname = $fname = $mname = $pos = $edept = $edcode = $aid = $leavetype = $other = $othertype = $withpay = $dateapplied = $no_days = $fromdate = $todate = $status = $remarks = "";
$message = "";
$lbal = 0;
$user = $this->session->userdata("username");
$bfp = $this->employee->getBudgetFinPres($user);
if($job == "delete"){
    if($this->employee->getClusterHead($user))  
            $tbl = "leave_app_chead";
    else{
        if($dept == "HR")   $tbl = "leave_app_hrd";
        else                $tbl = "leave_app_dhead";
    }    
    if($bfp)                $tbl = $bfp;
    $this->db->query("DELETE FROM $tbl WHERE id='{$id}'");
    $message = "Successfully Deleted..";
}
else if($job == "cancel"){
    $this->db->query("UPDATE leave_request SET status = 'CANCELED'  WHERE aid='{$idnum}'");
    $this->db->query("UPDATE leave_app SET status = 'CANCELED'  WHERE id='{$idnum}'");
	$query  = $this->db->query("SELECT * FROM employee_leave_credit WHERE employeeid='$employeeid' AND leavetype='$ltype' AND CURRENT_DATE BETWEEN dfrom AND dto");
	foreach($query as $row)
	{
		$this->db->query("UPDATE employee_leave_credit SET avail='".($query->row(0)->avail-1)."', balance='".($query->row(0)->balance+1)."' WHERE employeeid='$employeeid' AND leavetype='$ltype' AND CURRENT_DATE BETWEEN dfrom AND dto");
	}
    $message = "Successfully Canceled..";
}else if($job == "lview"){
    $sql = $this->db->query("SELECT a.id,a.employeeid,a.type,a.timefrom,a.timeto,a.other,a.othertype,a.timestamp,a.nodays,a.datefrom,a.dateto,a.status,a.reason,b.lname,b.fname,b.mname,c.description AS epos, d.description AS edept, d.code 
         FROM leave_app a 
         LEFT JOIN employee b ON a.employeeid = b.employeeid
         LEFT JOIN code_position c ON b.positionid = c.positionid
         LEFT JOIN code_office d ON b.deptid = d.code 
         WHERE a.employeeid='{$code}' AND a.id='{$idnum}'");
         foreach($sql->result() as $row){
            $base_id = $row->id;
            $lname = $row->lname;
            $fname = $row->fname;
            $mname = $row->mname;
            $pos   = $row->epos;
            $timefrom = $row->timefrom;
            $timeto = $row->timeto;
            $edept = $row->edept;
            $edcode= $row->code;
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
}else{
 if($code && $idnum){
	
    $param = "";
    if($this->employee->getClusterHead($user))  
        $tbl = "leave_app_chead"; 
    else if($this->employee->getUnivPhysician($user)){
        $tbl = "leave_app_uphy";
    }
    else if(($this->employee->campus_principal($user)) === true){
         $tbl = "leave_app_principal";
    }
    else{
        if($dept == "HR")   $tbl = "leave_app_hrd";
        else                $tbl = "leave_app_dhead";
    }
    if($bfp)                $tbl = $bfp;
    if(!empty($category))   $param = " AND a.status='$category'";
	
     $sql = $this->db->query("SELECT a.aid,a.employeeid,a.type,a.other,a.timestamp,a.nodays,a.datefrom,a.dateto,a.status,a.dateapproved,a.reason,b.lname,b.fname,b.mname,c.description AS epos, d.description AS edept, d.code 
     FROM $tbl a 
     LEFT JOIN employee b ON a.employeeid = b.employeeid
     LEFT JOIN code_position c ON b.positionid = c.positionid
     LEFT JOIN code_office d ON b.deptid = d.code 
     WHERE a.employeeid='{$code}' AND a.id='{$idnum}'  $param");
	 if($sql->num_rows() == 0)
	 {
		$sql = $this->db->query("SELECT a.aid,a.employeeid,a.type,a.other,a.timestamp,a.nodays,a.datefrom,a.dateto,a.status,a.dateapproved,a.reason,b.lname,b.fname,b.mname,c.description AS epos, d.description AS edept, d.code 
     FROM leave_app_dhead a 
     LEFT JOIN employee b ON a.employeeid = b.employeeid
     LEFT JOIN code_position c ON b.positionid = c.positionid
     LEFT JOIN code_office d ON b.deptid = d.code 
     WHERE a.employeeid='{$code}' AND a.id='{$idnum}'  $param"); 
	 }
     foreach($sql->result() as $row){
        $lname = $row->lname;
        $fname = $row->fname;
        $mname = $row->mname;
        $pos   = $row->epos;
        $edept = $row->edept;
        $edcode= $row->code;
        $aid   = $row->aid;
        $leavetype = $row->type;
        $other = $row->other;
        $dateapplied = date('m/d/Y',strtotime($row->timestamp));
        $no_days = $row->nodays;
        $fromdate = $row->datefrom;
        $todate = $row->dateto;
        $status = $row->status;
        $remarks = $row->reason;
		
     }
 }
 $isreadonly = "readonly='true'";
 $isdisabled = "disabled";

 if($CI->utils->getDeptHead('head','HR') == $user || $CI->utils->getDeptHead('divisionhead','HR') == $user){
    $isreadonly = $isdisabled = "";
 }

}
?>
<style>
.modal{
    width: 1000px;
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
<div class="modal-dialog">
<div class="modal-content">
	<!-- header -->
    <div class="modal-header">
        <table width="100%">
            <tr>
                <td rowspan="2" width="10%"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
            </tr>
            <tr>
                <td><strong>View Details</strong></td>
            </tr>
        </table>
    </div>
    <!-- end header -->

    <!-- body -->
    <div class="modal-body">
        <div class="content">
        	<form id="form_leave">
				<input hidden="" id="leavebal" value="" />
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
				<?if($other == "DA"){?>
				<div class="form_row">
				    <label class="field_name align_right">Leave Type</label>
				    
				        <div class="field">
				            <input type="checkbox" name="ltype" value="ABSENT" <?=($othertype == "ABSENT" ? " checked" : "")?> <?=$isdisabled?>/> ABSENT &nbsp;&nbsp;&nbsp;
				            <input type="checkbox" name="ltype" value="DIRECT" <?=($othertype == "DIRECT" ? " checked" : "")?> <?=$isdisabled?>/> OB &nbsp;&nbsp;&nbsp;
				            <!-- <input type="checkbox" name="ltype" value="UT/HD"  <?=($othertype == "UT/HD" ? " checked" : "")?> <?=$isdisabled?>/> UNDERTIME/HALFDAY &nbsp;&nbsp;&nbsp; -->
				            <input type="checkbox" name="ltype" value="NO PUNCH IN/OUT" <?=($othertype == "NO PUNCH IN/OUT" ? " checked" : "")?> <?=$isdisabled?>/> CORRECTION OF TIME IN/OUT &nbsp;&nbsp;&nbsp;
				        </div>
				    
				</div>
				<?}else{?>
				<div class="form_row">
				    <label class="field_name align_right">Leave Type</label>
				    <div class="field no-search">
				        <input type="checkbox" name="ltype" value="VL" <?=$leavetype == "VL" ? " checked" : ""?> disabled=""/> VACATION &nbsp;&nbsp;&nbsp;
				        <input type="checkbox" name="ltype" value="SL" <?=$leavetype == "SL" ? " checked" : ""?> disabled=""/> SICK &nbsp;&nbsp;&nbsp;
				        <input type="checkbox" name="ltype" value="EL" <?=$leavetype == "EL" ? " checked" : ""?> disabled=""/> EMERGENCY &nbsp;&nbsp;&nbsp;
				        <input type="checkbox" name="ltype" value="other" <?=$leavetype == "other" ? " checked" : ""?> disabled=""/> OTHER &nbsp;&nbsp;&nbsp;
				        <select name="othleave" id="othleave" style="width: 110px;" disabled=""><?=$this->employeemod->othLeave($other);?></select>
				    </div>
				</div>
				<?}?>
				<div class="form_row">
				    <label class="field_name align_right">With Pay?</label>
				    <div class="field no-search">
				        <select class="form-control" name="withpay" id="withpay" disabled=""><?=$this->employeemod->withPay();?></select>
				    </div>
				</div>
				<div class="form_row">
				    <label class="field_name align_right">Leave From</label>
				    <div class="field">
				        <div class="input-group date" id="datesetfrom" data-date="<?=$fromdate?>" data-date-format="yyyy-mm-dd" disabled="">
				            <input class="align_center" size="16" name="datesetfrom" type="text" value="<?=$fromdate?>" readonly>
				            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
				        </div>
				        <?if($othertype != "NO PUNCH IN/OUT"){?> 
				        <div class="input-group date" id="datesetto" data-date="<?=$todate?>" data-date-format="yyyy-mm-dd" disabled="">
				            <input class="align_center" size="16" name="datesetto" type="text" value="<?=$todate?>" readonly>
				            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
				        </div>
				        <?}?>
				    </div>
				</div>
				<!-- newly added by justin (with e) for #ica-hyperion 21090 -->
				<?if($othertype == "DIRECT"){
				    $timefrom = strtoupper(date('h:i a',strtotime($timefrom)));
				    $timeto = strtoupper(date('h:i a',strtotime($timeto)));
				?>
				<div class="form_row" id="hideTITO">
				    <label class="field_name align_right">Time In</label>
				        <div class="field">
				            <div class="input-group bootstrap-timepicker">
				                <input class="input-small align_center" type="text" name="tfrom" id="tfrom" value="<?=$timefrom?>" style="width: 125px;" <?=$isreadonly?> />
				                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
				            </div>
				            To
				            <div class="input-group bootstrap-timepicker">
				                <input class="input-small align_center" type="text" name="tto" id="tto" value="<?=$timeto?>" style="width: 125px;" <?=$isreadonly?> />
				                <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
				            </div>
				        </div>
				</div>
				<?}?>

				<!-- displayed time record -->
				<?if($othertype == "NO PUNCH IN/OUT"){?>
				    <div class="form_row" id="hideTable">
				        <label class="field_name align_right">My Time Record</label>
				        <div class="field">
				            <table class="table table-hover table-bordered datatable" id="tableId">
				                <thead>
				                    <th class="th-style" style="text-align: center;">Applied Time</th>
				                    <th class="th-style" style="text-align: center;">Approved Time</th>
				                    
				                </thead>
				                <tbody id="tbody-data">
				                    <? 
				                        $timerecord = $this->employeemod->findApplyTimeRecord($base_id);
				                        foreach ($timerecord as $row) {
				                            $style = "text-align: center; font-weight: bold;";
				                            if($row->status == "change") $style .= " background-color: #95bbd0;";
				                            if($row->status == "disapproved") $style .= " background-color: #d09298;";
				                    ?>
				                        <tr>
				                            <td style="<?=$style?>"><?=isset($row->timein)?strtoupper(date('h:i: a',strtotime($row->timein))) : "xx:xx xx"?> - <?=isset($row->timeout)?strtoupper(date('h:i: a',strtotime($row->timeout))) : "xx:xx xx"?></td>
				                            <td style="<?=$style?>"><?=isset($row->actual_timein)?strtoupper(date('h:i: a',strtotime($row->actual_timein))) : "xx:xx xx"?> - <?=isset($row->actual_timeout)?strtoupper(date('h:i: a',strtotime($row->actual_timeout))) : "xx:xx xx"?></td>
				                        </tr>
				                    <?  } // end of foreach
				                    ?>
				                </tbody>
				            </table>
				        </div>
				    </div><br>
				<?}?>
				<!-- end of newly added by justin (with e) for #ica-hyperion 21090 -->
				<?if($other != "DA"){?> 
				<div class="form_row">
				    <label class="field_name align_right">Days</label>
				    <div class="field no-search">
				        <input type="text" name="ndays" id="ndays" value="<?=$no_days?>"   <?=$isreadonly?> onkeypress="return numbersonly()" />
				    </div>
				</div>
				<?}?>
				<div class="form_row">
				    <label class="field_name align_right">Reason</label>
				    <div class="field no-search">
				        <textarea rows="4" style="width: 100%;resize: none;" name="reason" id="reason" placeholder="Reason" readonly=""><?=$remarks?></textarea>
				    </div>
				</div><br>
				<div class="form_row">
				    <label class="field_name align_right">Date Applied</label>
				    <div class="field">
				        <input class="col-md-8 required" id="mh_dateapplied" name="mh_dateapplied" <?=$isreadonly?> type="text" value="<?=$dateapplied?>"/>
				    </div>
				</div>
				<?if($job != "lview"){?>
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
				<?}?>
			</form>
        </div>
    </div>
    <!-- end body -->

    <!-- footer -->
    <div class="modal-footer">
        <div id="saving">
            <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
    <!-- end footer -->
</div>