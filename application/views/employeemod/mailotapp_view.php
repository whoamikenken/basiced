
<style>
    .form_row{
        margin-bottom: 20px;
    }    
</style>
<div class="modal-dialog">
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
            <center><b><h3 tag="title" class="modal-title">View Details</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="form_row">
                <label class="field_name align_right"><strong>Date From</strong></label>
                <div class="field">
                    <div class='input-group date' style="width: 57%;" id='datesetfrom' data-date="<?=$dfrom?>" data-date-format="yyyy-mm-dd" style="margin-bottom: 20px;">
                        <input type='text' class="form-control" size="16" name="datesetfrom" id="dfrom" type="text" value="<?=$dfrom?>"  readonly=""/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                    <div class='input-group date' id='datesetto' style="display: none;" data-date="<?=$dto?>" data-date-format="yyyy-mm-dd">
                        <input type='text' class="form-control" size="16" name="datesetto" id="dfrom" type="text" value="<?=$dto?>"  readonly=""/>
                        <span class="input-group-addon">
                            <span class="glyphicon glyphicon-calendar"></span>
                        </span>
                    </div>
                </div>
            </div>
            <div class="form_row" style="padding-bottom: 10px;">
                <label class="field_name align_right">Office Hour</label>
                <div class="field"  style="width: 76%;">
                    <input type="text" class="form-control align_center" id="roh" name="roh" value="<?=$sched?>"  readonly=""/>
                </div>
                <span id="loadingroh" hidden=""></span>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Time Start</strong></label>
                <div class="field">
                    <div class="col-md-5" style="padding-left: 0px;">
                        <div class='input-group time' style="width: 117%;">
                            <input type='text' class="form-control" name="tfrom" id="tfrom" value="<?=$tstart?>"  readonly=""/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                    </div>
                    <div>
                        <label class="col-md-1 align_right">End</label>
                    </div>
                    <div class="col-md-6" style="margin-left: -2%;">
                        <div class='input-group time'>
                            <input type='text' class="form-control" name="tto" id="tto" value="<?= $tend ?>"  readonly=""/>
                            <span class="input-group-addon">
                                <span class="glyphicon glyphicon-time"></span>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Applied Total Hour/s</strong></label>
                <div class="field no-search" style="width: 46%;">
                    <input class="form-control" type="text" name="ndays" id="ndays" value="<?=$total?>" readonly="" />
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Approved Total Hour/s</strong></label>
                <div class="field no-search" style="width: 46%;">
                    <input class="form-control" type="text" name="ndays" id="ndays" value="<?=$total_approved?>" readonly="" />
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right"><strong>Reason</strong></label>
                <div class="field">
                    <textarea class="form-control" rows="2" style="width: 100%;resize: none; background: #eee" name="reason" id="reason" placeholder="Speaker" readonly=""><?=$reason?></textarea>
                </div>
            </div>
            <div class="form_row">
                <label class="field_name align_right">Status</label>
                <div class="field no-search" style="width: 46%;">
                    <input class="form-control" type="text" value="<?=$status?>" readonly="" />
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
<script>
    $('.time').datetimepicker({
        format: 'LT'
    });

    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });
    $('.chosen').chosen();
</script>