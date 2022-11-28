<?php

/**
 * @author Justin
 * @copyright 2015
 */

$quarter  = $schedule = "";
$sdate    = $edate    = date('Y-m-d');
$ishidden = " hidden=''";

if($this->input->post("id")){
    $query = $this->payroll->displayCutoff($this->input->post("id"));
    $ishidden = "";
    $schedule    = $query->row(0)->schedule;
    $quarter    = $query->row(0)->quarter;
    $sdate  = $query->row(0)->startdate;
    $edate    = $query->row(0)->enddate;
}
//echo $this->input->post('id');
?>
<style>
.bootstrap-datetimepicker-widget.dropdown-menu.usetwentyfour.bottom{
}
</style>
<form id="cutoff">
<input name="model" value="newCutoff" hidden=""/>
<input name="id" value="<?=$this->input->post("id")?>" hidden="" />
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
             <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                    <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                     <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title">New Cut-Off</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="form-group">
                <label  class="col-sm-3 align_right">Schedule</label>
                <div class="col-sm-9">
                   <select class="chosen-select align_left form-control" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule($schedule);?></select>
                </div>
            </div>
            <div class="form-group" id="qload" <?=$ishidden?>><br></div>
            <div class="form-group" id="qshow" <?=$ishidden?>>
                <br>
                <label  class="col-sm-3 align_right">Quarter</label>
                <div class="col-sm-9">
                    <select class="chosen-select align_left form-control" name="quarter" id="quarter"><?=$this->payrolloptions->quarter($quarter,TRUE,$schedule);?></select>
                </div>
            </div>
            <br><br>
            <div class="form-group">
                <label  class="col-sm-3 align_right">Start Date</label>
                <div class="col-sm-9">
                    <div class='input-group date' id='dfrom' data-date="<?=$sdate?>" data-date-format="yyyy-mm-dd">
                        <input type='text' class="form-control" value="<?=$sdate?>" size="16" name="dfrom"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <br><br>
            <div class="form-group">
                <label  class="col-sm-3 align_right">End Date</label>
                <div class="col-sm-9">
                    <div class='input-group date' id='dfrom' data-date="<?=$edate?>" data-date-format="yyyy-mm-dd">
                        <input type='text' class="form-control" value="<?=$edate?>" size="16" name="dto"/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="save" class="btn btn-success">Save</button>
            </div>
        </div>
    </div>
</div>
</form>
<script>
$("#save").click(function(){
    var form_data   =   $("#cutoff").serialize();
    $("#saving").hide();
    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..")
    $.ajax({
       url      :   "<?=site_url("payroll_/loadmodelfunc")?>",
       type     :   "POST",
       data     :   form_data,
       success  :   function(msg){
        alert(msg);
        loadcutoffconfig();
        $("#close").click();
       }
    });
});
$("#schedule").change(function(){
    $("#qshow").hide();
    $("#qload").show().html("&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $(this).val(), 
          model     :   "quarter"
        },
        success: function(msg){
            // alert(msg);
           $("#qload").hide();
           $("select[name='quarter']").html(msg).trigger("liszt:updated");
           $("#qshow").show();
        }
    });
});

$('#dfrom,#dto').datetimepicker({
     format:'YYYY-MM-DD'
    });

</script>