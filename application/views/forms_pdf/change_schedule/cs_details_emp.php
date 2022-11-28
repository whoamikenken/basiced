<?php
/**
 * @modified Angelica Arangco  2017
 */
 $isreadonly = "readonly='true'";
 $isdisabled = "disabled";
 $ishidden   = ($colhead=='hrhead'?'':'hidden=""');


?>
<style>
.modal{
    width: 75%;
    left: 0;
    right: 0;
    margin: auto;
}
#reason
{
  resize: none;
}
</style>
<form id="frmapproved">
<input name="id" value="<?=$csid?>" hidden="" />
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
                    <span style="line-height: 5px;"><b><?=$fullname?></b></span>
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
                <label class="field_name align_right">Date Active</label>
                <div class="field">
                    <div class="input-group date" id="dfrom" data-date="<?=$date_effective?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="dfrom" type="text" value="<?=$isTemporary? '' : $date_effective?>" readonly>
                        <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                    </div>
                </div>
            </div>

            <!-- ///< For change schedule on specific dates only -->

             <div class="form_row">
                <div class="field"  style="padding-bottom: 10px;">
                 <input type="checkbox" class="double-sized-cb" name="specific" value="1" <?=$isTemporary?" checked":"" ?>>&nbsp;&nbsp; <b>Check this if specific dates only</b>
                    &nbsp;&nbsp;From
                    <div class="input-group date" id="start" data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="start" type="text" value="<?=$dfrom?>" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                    &nbsp;&nbsp;To&nbsp;&nbsp;
                    <div class="input-group date" id="end" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                        <input class="align_center" size="16" name="end" type="text" value="<?=$dto?>" readonly>
                        <span class="add-on">&nbsp;<i class="glyphicon glyphicon-calendar"></i>&nbsp;</span>
                    </div>
                </div>
            </div>
			
            <div class="form_row">
                <label class="field_name align_right" style="line-height: 5px;">Status</label>
                <div class="field">
                    <input type="text" name="" value="<?=$status?>" readonly>
                </div>
            </div>
            <div class="form_row" style="border: transparent !important;">
                <table class="table table-striped table-bordered table-hover" width="100%">
                    <thead>
                        <tr>
                            <th rowspan="2">Day of Week</th>
                            <th rowspan="2">From</th>
                            <th rowspan="2">To</th>
                            <th colspan="2" <?=$ishidden?>>First Half</th>
                            <th colspan=""  <?=$ishidden?>>Second Half</th>
                            <th rowspan="2" <?=$ishidden?>>Early Dismissal</th>
                            <th class="align_center" rowspan="2">Lec</th>
                            <th class="align_center" rowspan="2">Lab</th>
                        </tr>
                        <tr <?=$ishidden?>>
                            <th>Tardy Start</th>
                            <th>Absent Start</th>
                            <th>Half Day Absent</th>
                        </tr>
                    </thead>
                    
                    <tbody id="schedule">
                    <?
                    if($csdata){
                        foreach($csdata as $row){
                            ?>
                                <tr tag="grp" detail_id="<?=$row->id?>" dayofweek="<?=$row->day_code?>" dayidx="<?=$row->day_index?>">
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
                                
                                    <td <?=$ishidden?>>
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="tardy_f" value="<?=date("h:i A",strtotime($row->tardy_start))?>" />
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                
                                    <td <?=$ishidden?>>
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="absent_f" value="<?=date("h:i A",strtotime($row->absent_start))?>"  />
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                
                                    <td <?=$ishidden?>>
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="absent_e" value="<?=date("h:i A",strtotime($row->absent_half_start))?>"  />
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                    
                                    <td <?=$ishidden?>>
                                      <div class="input-group bootstrap-timepicker">
                                        <input class="input-small align-center" type="text" name="early_d" value="<?=date("h:i A",strtotime($row->early_dismissal))?>" />
                                        <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
                                      </div>
                                    </td>
                                    
                                    <td class="align_center"><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LEC" <?=$row->leclab == "LEC" ? " checked" : ""?> /></td>
                                    <td class="align_center"><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LAB" <?=$row->leclab == "LAB" ? " checked" : ""?> /></td>
                                    
                                  </tr>
                            <?
                        }
                    }?>
                    </tbody>
                </table>
            </div>
             <br>
            <div class="form_row" style="border: transparent !important;">
            <label class="field_name align_left " style="width: 5%">Reason</label>
            <div style="margin-left:10px;">
                    <textarea rows="4" class="align_left" name="reason" id="reason" style="width: 95%;" placeholder="Reason" required="" readonly=""><?=$row->reason?></textarea>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>

</script>