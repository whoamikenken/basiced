<?php

/**
 * @author Justin
 * @copyright 2016
 */

$deptcode = $this->employee->getHeadDeptCode($this->session->userdata('username'));
$datetoday = date("d-m-Y");
$isread = $ishidden = "";
$id   = $this->input->post("idkey");
$indi = $this->input->post("indi"); 
if($id){
    $query = $this->employeemod->loadschedreqdata($id);
    if($query->num_rows() > 0){
        foreach($query->result() as $row){
            $lname = $row->lname;
            $fname = $row->fname;
            $mname = $row->mname;
            $pos   = $row->epos;
            $edept = $row->edept;
            $isread= $row->isread;
            $chead = $row->chead;
            $cheadstatus= $row->cheadstatus;
            $hrd   = $row->hrd;
            $hrdstatus=$row->hrdstatus;
            $status= $row->status;
        }
    }
    if($indi && !$isread)   $this->employeemod->markasread(array("tbl"=>"change_schedule_request","id"=>$idkey,"val"=>1));
    if($chead == $this->session->userdata('username')){
       $this->employeemod->markasread(array("tbl"=>"change_schedule_request_chead","id"=>$idkey,"val"=>1));
       if(in_array($cheadstatus,array("APPROVED","DISAPPROVED")))    $ishidden = " style='display: none;'";
    }
    if($hrd == $this->session->userdata('username')){
       $this->employeemod->markasread(array("tbl"=>"change_schedule_request_hrd","id"=>$idkey,"val"=>1));
       if(in_array($hrdstatus,array("APPROVED","DISAPPROVED")))    $ishidden = " style='display: none;'";
    }
}
?>
<style>
.modal{
    width: 75%;
    left: 0;
    right: 0;
    margin: auto;
}
.leclab
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
  pointer-events: none;
}
</style>
<form id="frmapproved">
<input name="model" value="approvedSched" hidden=""/>
<input name="id" value="<?=$id?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <table width="100%">
                <tr>
                    <td rowspan="2" width="70px"><img src="<?=base_url()?>/images/school_logo.jpg" /></td>
                    <td><h4 class="modal-title"><strong><?=$this->extras->school_name()?></strong></h4></td>
                </tr>
                <tr>
                    <td><strong>Change Schedule Request Form</strong></td>
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
            <?if(in_array($this->session->userdata('username'),array($chead,$hrd))){?>
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Status</label>
                    <div class="field">
                        <div class="no-search" <?=$ishidden?>>
                            <select class="form-control" name="status" id="status" <?= (in_array($status,array("APPROVED","DISAPPROVED")) ? $isdisabled : "")?>>
                                <?
                                    $opt_status = $this->extras->showLeaveStatus();
                                    foreach($opt_status as $c=>$val){
                                ?><option<?=($c==$status ? " selected" : "")?> value="<?=$c?>" ><?=($val=="PENDING"?"Select status..":$val)?></option><?    
                                }
                                ?>
                            </select>
                        </div>
                        <span style="line-height: 5px; <?=(!$ishidden ? "display: none;" : "")?>"><b><?=$cheadstatus?></b></span>
                    </div>
            </div>
            <?}?>
            <div class="form_row" style="border: transparent !important;">
                <table class="table table-striped table-bordered table-hover" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2">Day of Week</th>
                            <th rowspan="2">From</th>
                            <th rowspan="2">To</th>
                            <th colspan="2" hidden="">First Half</th>
                            <th colspan=""  hidden="">Second Half</th>
                            <th rowspan="2" hidden="">Early Dismissal</th>
                            <th class="align_center" rowspan="2">Lec</th>
                            <th class="align_center" rowspan="2">Lab</th>
                        </tr>
                        <tr hidden="">
                            <th>Tardy Start</th>
                            <th>Absent Start</th>
                            <th>Half Day Absent</th>
                        </tr>
                    </thead>
                    
                    <tbody id="schedule">
                    <?
                    if($id){
                    $query = $this->employeemod->loadschedreqset($id);
                    if($query->num_rows() > 0){
                        foreach($query->result() as $row){
                            ?>
                                <tr>
                                    <td><?=$row->day_name?></td>
                                    <td>
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="fromtime" value="<?=date("h:i A",strtotime($row->starttime))?>" readonly="" />
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                
                                    <td>
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="totime" value="<?=date("h:i A",strtotime($row->endtime))?>" readonly="" />
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                
                                    <td hidden="">
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="tardy_f" value="<?=date("h:i A",strtotime($row->tardy_start))?>" readonly="" />
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                
                                    <td hidden="">
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="absent_f" value="<?=date("h:i A",strtotime($row->absent_start))?>" readonly="" />
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                
                                    <td hidden="">
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="absent_e" value="<?=date("h:i A",strtotime($row->absent_half_start))?>" readonly="" />
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                    
                                    <td hidden="">
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="early_d" value="<?=date("h:i A",strtotime($row->early_dismissal))?>" readonly=""/>
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                    
                                    <td class="align_center"><input type="checkbox" class="leclab" name="leclab" value="LEC" <?=$row->leclab == "LEC" ? " checked" : ""?> /></td>
                                    <td class="align_center"><input type="checkbox" class="leclab" name="leclab" value="LAB" <?=$row->leclab == "LAB" ? " checked" : ""?> /></td>
                                    
                                  </tr>
                            <?
                        }
                    }
                    }?>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <?if(in_array($this->session->userdata('username'),array($chead,$hrd)) && $status == "PENDING" && !$ishidden){?>
                <button type="button" id="approve" class="btn btn-danger" data-dismiss="modal">Save</button>
                <?}?>
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</form>
<?if(($indi && !$isread) || (in_array($this->session->userdata('username'),array($chead,$hrd)))){?><script>$("#close").click(function(){location.reload()});</script><?}?>
<?if(in_array($this->session->userdata('username'),array($chead,$hrd))){?>
<script>
$("#approve").click(function(){
    var form_data = $("#frmapproved").serialize();
    $.ajax({
      url: "<?=site_url("employeemod_/loadmodelfunc")?>",
      data : form_data,
      type : "POST",
      success:function(msg){
        alert(msg);
        location.reload();
      }
   });
});
$(".chosen").chosen();
</script>
<?}?>