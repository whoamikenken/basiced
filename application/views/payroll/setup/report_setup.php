<?php

/**
 * @author Angelica
 * @copyright 2018
 */
?>

<style>
/*div.options{
    padding-top: 5px;
}

div.options > label > input {
    visibility: hidden;
}*/

/*div.options > label {*/
    /*display: block;*/
    /*margin: 0 0 0 -10px;*/
    /*padding: 0 0 20px 0;  */
    /*height: 10px;*/
    /*width: 150px;*/
    
/*}*/

/*div.options > label > img {
    display: inline-block;
    padding: 0px;
    height:20px;
    width:20px;
    background: none;
}

div.options > label > input:checked +img {  
    background: url("<?=base_url()?>images/greencheck.png?>");
    background-repeat: no-repeat;
    background-position:center center;
    background-size:20px 20px;
}*/
</style>


<form id="frmsetup">
<input name="reportname" value="<?=$reportname?>" hidden="" />
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
                <center><b><h3 tag="title" class="modal-title"><?=(isset($title) && $title != "undefined"? $title : "Setup")?></h3></b></center>
        </div>
        <div class="modal-body">
            <div class="content">
                <?
                switch ($reportname) {
                    case 'netPayHistory': ?>

                            <!-- ********************* NET PAY HISTORY ************************ -->
                            <div class="form_row">
                                <label class="field_name align_right">Period Cover:</label>
                                <div class="field no-search">
                                    <div class="col-md-6" style="padding-left: 0px;"><select class="chosen-select form-control" name="period" id="period"><?=$monthlist?></select></div>
                                    <div class="col-md-6" style="padding-right: 0px;"><select class="chosen-select form-control" name="pyear" id="pyear" style="width: 100px;"><?=$yearlist?></select></div>
                                </div>
                            </div>
                            <br>
                          <div class="form_row">
                            <label class="field_name align_right">Status:</label>
                            <div class="field no-search">
                                <select class="chosen-select col-md-2" name="status" id="status">
                                <option value="PROCESSED">Processed</option>
                                <option value="PENDING">Pending</option>
                                <option value="SAVED">Saved</option>
                                </select>
                            </div>
                          </div>
                            <br>
                          <div class="form_row">
                            <label class="field_name align_right">Sort:</label>
                            <div class="field no-search">
                                <select class="chosen-select col-md-2" name="sort" id="sort">
                                <option value="office">Office</option>
                                <option value="alpha">Alphabetical Order</option>
                                </select>
                            </div>
                          </div>
                          <br>
                            
                        <?
                        break;

                    case 'pagibigFileWriter': ?>

                            <!-- ********************* PAGIBIG FILE WRITER ************************ -->
                            <div class="form_row">
                                <label class="field_name align_right">Cutoff</label>
                                <div class="field no-search">
                                    <select class="form-control" name="cutoff"><?=$cutofflist?></select>
                                </div>
                            </div>
                            <div class="form_row">
                                <label class="field_name align_right">Bank</label>
                                <div class="field no-search">
                                    <select class="form-control" name="bank"><?=$banklist?></select>
                                </div>
                            </div>
                            
                        <?
                        break;

                    case 'philhealthFileGenerator':?>
                        <div class="form_row">
                            <label class="field_name align_right">Month</label>
                            <div class="field no-search">
                                <select class="form-control" name="month" id="month"><?=$monthlist?></select>
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                                Year
                                <select class="form-control" name="pyear" id="pyear" style="width: 100px;"><?=$yearlist?></select>
                            </div>
                        </div>
                    <?    
                        break;
                    
                    default:
                        # code...
                        break;
                } ?>


                <?

                    if(in_array($reportname, $reports_wd_format_filter)){ ?>
                        <div class="form_row">
                            <label class="field_name align_right">Format</label>
                            <div class="field">
                                <div class="options">
                                    <label title="PDF">
                                        <input type="radio"  name="reportformat" value="pdf" checked="" /> 
                                        <img />
                                        PDF
                                   </label> &emsp;
                                    <label title="XLS">
                                        <input type="radio" name="reportformat" value="xls"  />
                                        <img />
                                        EXCEL
                                    </label>  
                                    
                                </div>
                            </div>
                        </div>
                    <? }

                ?>


            </div>
        </div>

        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="generateReport" class="btn btn-success">Generate</button>
            </div>
        </div>


    </div>
</div>
</form>

<script>
    $('#generateReport').on('click',function(){
        $("#frmsetup").attr("target", "_blank");
        $("#frmsetup").attr("action", "<?=site_url("reports_/loadPayrollReport")?>");
        $("#frmsetup").attr("method", "post");
        $("#frmsetup").submit();
    });

    $(".chosen-select").chosen();
</script>