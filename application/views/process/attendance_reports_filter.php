<?
/**
* @author justin (with e)
* @copyright 2018
*/

$date = date("Y-m-d");
?>
<form id="frm-attendance-report">
<div class="modal-dialog">
    <div class="modal-content">
        <div class="modal-header">
            <div class="media">
                <div class="media-left">
                    <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                </div>
                <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
                    <h4 class="media-heading"  style="font-size: 18px !important"><b>Pinnacle Technologies Inc.</b></h4>
                    <p style="font-family:Avenir; margin-top: -1%; font-size: 16px !important; font-weight: 300 !important">D`Great</p>
                </div>
            </div>
            <center><b><h3 tag="title" class="modal-title">OB/Excuse Slip</h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
            <?
            switch ($filter) {
            	case 'absent-ob-excuse-slip':
            ?>
                <div class="form_row">
                <label class="field_name align_right" id="lblFrom">Date From</label>
                    <div class="field">
                        <div class="col-md-12"  style="padding: 0px;">
                            <div class="col-md-5" style="padding-left: 0px;">
                                <div class='input-group date' id="dfrom" data-date="<?=$date?>" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="dfrom" type="text" value="<?=$date?>"/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-2" >
                                <label class="align_center">To</label>
                            </div>
                            <div class="col-md-5"  style="padding-right: 0px;">
                                <div class='input-group date' id="dto" data-date="<?=$date?>" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="dto" type="text" value="<?=$date?>"/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right">Status By</label>
                    <div class="field no-search">
                        <select class="form-control chosen" name="isactive" id="isactive">
                            <option value="">All Status</option>
                        <option value="1" selected>Active</option>
                        <option value="0">Inactive</option>
                        </select>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right">Type</label>
                    <div class="field no-search">
                        <select class="form-control chosen" name="type" id="type">
                            <option value="">- All Type -</option>
                            <!-- <option value="ABSENT">ABSENT</option> -->
                            <option value="DIRECT">OFFICIAL BUSSINESS</option>
                            <option value="CORRECTION">CORRECTION FOR TIME IN/OUT</option>
                        </select>
                    </div>
                </div><br>
                <div class="form_row">
                    <label class="field_name align_right">Format</label>
                    <div class="field no-search">
                        <select class="form-control chosen" name="format" id="format">
                            <option value="PDF">PDF</option>
                            <option value="EXCEL">EXCEL</option>
                        </select>
                    </div>
                </div>
            <?
            		break;
            }
            ?>
            </div>
        </div>
        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="generate" class="btn btn-success">Generate</button>
            </div>
        </div>
    </div>
</div>
</form>
<script type="text/javascript">
$("#generate").unbind("click").click(function(){
    if("<?=$filter?>" == "absent-ob-excuse-slip"){
        $("#frm-attendance-report").attr("target", "_blank");
        $("#frm-attendance-report").attr("action", "<?=site_url("reports_/showAbsentOBCorrectionReport")?>");
        $("#frm-attendance-report").attr("method", "post");
        $("#frm-attendance-report").submit();
    }
});

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();
</script>