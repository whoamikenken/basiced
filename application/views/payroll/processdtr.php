<style type="text/css">
    .chosen-container{
        text-align: left;
    }
</style>
<?php
    $campus_list = $this->extras->getCampusDescription();
?>
<div class="panel">
    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Process DTR</b></h4></div>
    <div class="panel-body">
        <form id="frm-process-dtr" class="form-horizontal">
            <div class="row">
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group" style="width: 70%;padding-left: 10px; <?= (count($campus_list) > 1) ? '' : 'pointer-events: none;'; ?>">
                            <label class="field_name align_right">Campus</label>
                            <div class="field">
                                <select class="chosen" id="campus" name="campus">
                                    <?php
                                        if(count($campus_list) > 1){
                                            ?>
                                                <option value="">All Campus</option>
                                            <?php
                                        }    
                                    ?>
                                    <?php foreach ($campus_list as $key => $value): ?>
                                        <option value="<?=$key?>"><?=$value?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="width: 70%;padding-left: 10px;">
                            <label class="field_name align_right">Department</label>
                            <div class="field">
                                <select class="deptid form-control chosen" id="deptid" name="deptid">
                                    <option value="">All Department</option>
                                    <?php 
                                    $opt_department = $this->extras->showdepartment();
                                    foreach($opt_department as $c=>$val): ?>
                                        <option value="<?=$c?>"><?=$val?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group" style="width: 70%;padding-left: 10px;">
                            <label class="field_name align_right">Cut-Off</label>
                            <div class="field">
                                <select class="chosen" id="cutoff" name="cutoff"><?=$this->employeemod->displayCutOff()?></select>
                                <span class="error-msg" id="cutoffMsg"></span>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="width: 70%;padding-left: 10px;">
                            <label class="field_name align_right">Office</label>
                            <div class="field">
                                <select class="office form-control chosen" id="office" name="office">
                                    <option value="">All Office</option>
                                    <?php 
                                    $opt_department = $this->extras->showoffice();
                                    foreach($opt_department as $c=>$val): ?>
                                        <option value="<?=$c?>"><?=$val?></option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group" style="width: 70%;padding-left: 10px;">
                            <label class="field_name align_right">Type</label>
                            <div class="field">
                                <select class="chosen" id="tnt" name="tnt">
                                    <option value="teaching">Teaching</option>
                                    <option value="nonteaching">Non Teaching</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="width: 70%;padding-left: 10px;">
                            <label class="field_name align_right">Employment Status</label>
                            <div class="field">
                                <select class="chosen" name="empstat" id="empstat">
                                    <?php
                                  $empstatuses = $this->extras->showemployeestatus('All Employment Status');
                                  foreach ($empstatuses as $key => $item) {
                                    ?>
                                    <option value='<?=$key?>'><?= ucfirst (strtolower ($item)); ?></option>
                                    <?php
                                  }
                                ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="width: 70%;padding-left: 10px;">
                            <label class="field_name align_right">Status</label>
                            <div class="field">
                                <select class="chosen" name="empstatus" id="empstatus">
                                    <option value="">All Status</option>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group" style="width: 70%;padding-left: 10px;">
                            <label class="field_name align_right">Employee</label>
                            <div class="field">
                                <select class="chosen" name="employeeid" id="empids">
                                    <option value="">All Employee</option>
                                    <?php foreach($emplist as $empid => $fullname): ?>
                                        <option value="<?=$empid?>"><?=$fullname?></option>   
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <br>
        <div class="row no-search">
            <div class="field" style="padding-left: 10px;">
                <button class="btn btn-primary" id='confirmrep' style="margin-top: 5px;"> Confirmation Report</button>
                <button class="btn btn-primary" id="payrollrep"  style="margin-top: 5px;"> Attendance Report</button>
                <button class="btn btn-primary" id="payrollrepReportSummary" style="margin-top: 5px;">Attendance Report (Print)</button>
                <button class="btn btn-primary" id="payrollrepIndividual" style="margin-top: 5px;"> Individual Report</button>
                <button class="btn btn-primary" id="displayAttendanceModal" style="margin-top: 5px;">Individual Report (Print)</button>
                <button class="btn btn-primary" id="butt_displayLackInOut" style="margin-top: 5px;">Lacking of In/Out</button>
                <button class="btn btn-primary" id="butt_report" style="margin-top: 5px;">Attendance Detailed Report</button>
                <button class="btn btn-primary" rtype='leavereport' reportTitle="Leave" id="btn-leave-report" style="margin-top: 5px;">Leave Report</button>
                <button class="btn btn-primary" rtype='attendancereportperday' id="btn-attendace-report" style="margin-top: 5px;">Attendance Report Per Day</button>
             </div>
        </div>
        <br />
    </div>
</div>
<div class="modal fade" id="print_batch_attendance" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
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
                <center><b><h3 tag="title" class="modal-title">Print Attendance</h3></b></center>
            </div>
            <div class="modal-body" style="margin-bottom: auto;">
                <div class="row">
                    <div tag='display'>
                        <div class="form_row">
                            <label class="field_name align_right">Format</label>
                            <div class="field" style="width: 75%;">
                                <select class="chosen col-md-6 attendanceReport">
                                    <option value="butt_print_att" id="butt_print_att" name="attendancePDF">PDF</option>
                                    <option value="" id="attendanceExcel" name="attendanceExcel">EXCELL</option>
                                </select>                                       
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer" style="margin-top: 5%;">
                    <button type="button" class="btn btn-success" id='GenerateReport'>Generate</button>
                    <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="print_payrollrepReportSummary" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">
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
                <center><b><h3 tag="title" class="modal-title">Click Checkbox for Filtering</h3></b></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div tag='display'>
                        <div class="category" align="center">
                            <form id="frm-print">
                                <input type="hidden" name="cutoff_date" value="cutoff_date">
                                <input type="hidden" name="teaching_type" value="teaching_type">
                                <input type="hidden" name="empid_list" value="empid_list">
                                <input type="hidden" name="dept_keys">
                                <input type="hidden" name="office_keys">
                                <input type="hidden" name="sortby">
                                <input type="hidden" name="empstat_">

                                <!-- <input type="checkbox" id="campuscheck" name="campus" value="campus" /> Per Campus -->
                                <input type="checkbox" class="perdept" id="perdept" name="category" value="department" /> Per Department
                                <input type="checkbox" name="category" id="persort" value="alphabetically" /> Sort Alphabetically
                                <br><br>
                                <div class="form_row" id="campus_hide" hidden>
                                    <div class="field col-md-10 campus_hide" style="margin-left: 50px;">
                                        <div class="col-md-4" style="text-align: right;"><label>Campus:</label></div>
                                        <div class="col-md-8" style="padding-left: 0px;">
                                            <select class="chosen" id="campus" name="campus"><?=$this->extras->getCampuses()?></select>
                                        </div>
                                     </div>
                                </div>
                               <!--  <div class="form_row " id="departments" hidden>
                                    <div class="field col-md-10 department_hide" id="department_hide" style="margin-left: 50px;">
                                        <div class="col-md-4" style="text-align: right;"><label>Department:</label></div>
                                        <div class="col-md-8" style="padding-left: 0px;">
                                            <select class="dept_keys form-control chosen" name="dept_keys">
                                                <option value="">All Department</option>
                                                <?php 
                                                $opt_department = $this->extras->showoffice();
                                                foreach($opt_department as $c=>$val): ?>
                                                    <option value="<?=$c?>"><?=$val?></option>
                                                <?php endforeach ?>
                                            </select>
                                        </div>
                                     </div>
                                </div> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id='payrollrepReport'>Generate</button>
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
            </div>
        </div>
    </div>
</div>
<form id="attFrm">
    <input type="hidden" name="datesetfrom">
    <input type="hidden" name="datesetto">
    <input type="hidden" name="fv">
    <input type="hidden" name="edata">
    <input type="hidden" name="tnt">
    <input type="hidden" name="office">
    <input type="hidden" name="estatus">
    <input type="hidden" name="toks">
    <input type="hidden" name="form">
</form>
<div id="cutofflist"></div>

<a href="#" data-toggle="modal" data-target="#myModal" hidden id="open-modal"></a>
<div class="modal fade" id="myModalatt" data-backdrop="static" role="dialog"></div>
<div class="modal fade" id="myModal" data-backdrop="static"></div><br>
<script src="<?=base_url()?>js/attendance/process_dtr.js"></script>

<script type="text/javascript">
    var print_report = '';

    $("#displayAttendanceModal").click(function(){
        // $("#print_batch_attendance").modal("toggle");
        generatePDFReport();
    });

    $("#payrollrepReportSummary").click(function(){
        if($("#office").val() == "") $("#print_payrollrepReportSummary").modal("toggle");
        else $("#payrollrepReport").click();
        
    });
        $('#department_hide').hide();

    $("#perdept").click(function () {
           if($(this).prop("checked") == true){
            $(".department_hide").show();
            $("#campus_hide").hide();
            $("#campuscheck").prop("checked", false);
        }else {
            $(".department_hide").hide();
            $("#campus_hide").hide();
        }
    });

    $("#campuscheck").click(function () {
           if($(this).prop("checked") == true){
            $("#campus_hide").show();
            $(".department_hide").hide();
            $("#perdept").prop("checked", false);
            $("#persort").prop("checked", false);
        }else {
            $("#campus_hide").hide();
            $(".department_hide").hide();
        }
    });

    $("#persort").click( function () {
           if($(this).prop("checked") == true){
            $("#campuscheck").prop("checked", false);
            $(".department_hide").hide();
            $("#campus_hide").hide();
        }
    });
    $('.chosen').chosen();
</script>