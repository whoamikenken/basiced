<?php

/**
 * @author Ken
 * @copyright 2019
 */

?>
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content">
<div class="widgets_area">
    <div class="row">
        <div class="col-md-12">
          <div class="panel animated fadeIn delay-1s">
           <div class="panel-heading" style="background-color: #0072c6;"><center><h4><b><i class="glyphicon glyphicon-print">&nbsp;</i>HR REPORTS</b></h4></center></div>
           <div class="panel-body">
            <div class="col-md-6"><center><h2><b><i class="glyphicon glyphicon-paperclip"></i>&nbsp;&nbsp;&nbsp;Employee Information</b></h2></center>
                        <div class="col-sm-12" style="margin-top: 15px;">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover">
                                <a href="#" class="list-group-item rgen" report_title="Personnel Roster" data-toggle="modal" data-target="#myModal" rtype="personalroster">
                                    <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Personnel Roster Report</b></h4>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Personnel Roster" data-toggle="modal" data-target="#myModal" rtype="personalrosterxls">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Personnel Roster (Excel) Report</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Head Count" data-toggle="modal" data-target="#myModal" rtype="headcount">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Head Count Report</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Schedule" data-toggle="modal" data-target="#myModal" rtype="listemployeeschedule">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Schedule</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                          <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Birthday" data-toggle="modal" data-target="#myModal" rtype="employeebirthdayreport">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Birthday</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                          <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Department" data-toggle="modal" data-target="#myModal" rtype="employeelistdeptreport">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Department</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                          <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Office" data-toggle="modal" data-target="#myModal" rtype="employeelistperoffice">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Office</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                          <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Employment Status" data-toggle="modal" data-target="#myModal" rtype="employeelistbyemployment">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Employment Status</b></h4>
                                  </a>
                            </div>
                          </div>
                        <div class="col-sm-12">
                          <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Position" data-toggle="modal" data-target="#myModal" rtype="employeelistbyposition">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Position</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                          <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Salary" data-toggle="modal" data-target="#myModal" rtype="employeelistbysalary">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Salary</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                          <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Years Of Service" data-toggle="modal" data-target="#myModal" rtype="employeelistbyservice">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Years Of Service</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                          <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Gender" data-toggle="modal" data-target="#myModal" rtype="employeelistbygender">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Gender</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                          <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Civil Status" data-toggle="modal" data-target="#myModal" rtype="employeelistbycivilstatus">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employee List By Civil Status</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Seminar" data-toggle="modal" data-target="#myModal" rtype="seminarreport">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Seminar Report</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Employees Without Seminars/Workshop" data-toggle="modal" data-target="#myModal" rtype="employeewoseminar">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employees Without Seminars/Workshop Report</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Employment History" data-toggle="modal" data-target="#myModal" rtype="employmenthistoryreport">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employment History Report</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Employees With Sync Logs" data-toggle="modal" data-target="#myModal" rtype="empsynclogs">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Employees With Sync Logs</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Lock Account History" data-toggle="modal" data-target="#myModal" rtype="lockACcount">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Lock Account History Report</b></h4>
                                  </a>
                            </div>
                          </div>
                          <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Unlock Account History" data-toggle="modal" data-target="#myModal" rtype="unlockedACcount">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Unlock Account History Report</b></h4>
                                  </a>
                            </div>
                          </div>
                          
                </div>
            <div class="col-md-6"><center><h2><b><i class="glyphicon glyphicon-paperclip"></i>&nbsp;&nbsp;&nbsp;Attendance Reports</b></h2></center>
                        <div class="col-sm-12" style="margin-top: 15px;">
                            <div class="list-group" data-toggle="popover" data-placement="left" data-container="body" data-trigger="hover" >
                                <a href="#" class="list-group-item att-rgen" report_title="OB/Excuse Slip Report" data-toggle="modal" data-target="#myModal" rtype='absent-ob-excuse-slip'>
                                    <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>OB/Excuse Slip Report</b></h4>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-12" style="margin-top: 15px;">
                            <div class="list-group" data-toggle="popover" data-placement="left" data-container="body" data-trigger="hover" >
                                <a href="#" class="list-group-item facialDowntime">
                                    <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Facial Downtime Report</b></h4>
                                </a>
                            </div>
                        </div>
                        <div class="col-sm-12">
                            <div class="list-group" data-toggle="popover" data-placement="right" data-container="body" data-trigger="hover" >
                                  <a href="#" class="list-group-item rgen" report_title="Attendance Confirmed History" data-toggle="modal" data-target="#myModal" rtype="confirmed_history">
                                      <h4 class="list-group-item-heading"><b><i class="glyphicon glyphicon-print">&nbsp;&nbsp;</i>Attendance Confirmed History</b></h4>
                                  </a>
                            </div>
                          </div>
                        <div class="form_row" hidden>
                            <label class="field_name align_right"><i class="icon icon-print large" style="font-size: 26px;"></i></label>
                            <div class="field">
                                <div class="col-md-12" style="padding: 10px;">
                                    <span><a href="#" class="rgen"  report_title="Monthly Tardiness & Undertime Report" data-toggle="modal" data-target="#myModal" rtype='mureport'><b>Monthly Tardiness &amp; Undertime Report</b></a></span>
                                </div>
                            </div>
                        </div>
                        <div class="form_row" hidden>
                            <label class="field_name align_right"><i class="icon icon-print large" style="font-size: 26px;"></i></label>
                            <div class="field">
                                <div class="col-md-12" style="padding: 10px;">
                                    <span><a href="#" class="rgen"  report_title="Overtime Report" data-toggle="modal" data-target="#myModal" rtype='otreport'><b>Overtime Report</b></a></span>
                                </div>
                            </div>
                        </div>
                        <div class="form_row" hidden>
                            <label class="field_name align_right"><i class="icon icon-print large" style="font-size: 26px;"></i></label>
                            <div class="field">
                                <div class="col-md-12" style="padding: 10px;">
                                    <span><a href="#" class="rgen"  report_title="Summary of Absences (No leave filed)" data-toggle="modal" data-target="#myModal" rtype='summaryabsences'><b>Summary of Absences (No leave filed)</b></a></span>
                                </div>
                            </div>
                        </div>
                        <div class="form_row" hidden>
                            <label class="field_name align_right"><i class="icon icon-print large" style="font-size: 26px;"></i></label>
                            <div class="field">
                                <div class="col-md-12" style="padding: 10px;">
                                    <span><a href="#" class="rgen"  report_title="Detailed Summary of Absences" data-toggle="modal" data-target="#myModal" rtype='detailedsummaryabsences'><b>Detailed Summary of Absences</b></a></span>
                                </div>
                            </div>
                        </div>
                        <div class="form_row" hidden>
                            <label class="field_name align_right"><i class="icon icon-print large" style="font-size: 26px;"></i></label>
                            <div class="field">
                                <div class="col-md-12" style="padding: 10px;">
                                    <span><a href="#" class="rgen"  report_title="Summary of Absences w/ SL, VL and EL" data-toggle="modal" data-target="#myModal" rtype='summaryabsenceswelsl'><b>Summary of Absences w/ SL, VL and EL</b></a></span>
                                </div>
                            </div>
                        </div>
                        <div class="form_row" hidden>
                            <label class="field_name align_right"><i class="icon icon-print large" style="font-size: 26px;"></i></label>
                            <div class="field">
                                <div class="col-md-12" style="padding: 10px;">
                                    <span><a href="#" class="rgen"  report_title="Leave Report" data-toggle="modal" data-target="#myModal" rtype='leavereport'><b>Leave Report</b></a></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal fade" id="myModal" data-backdrop="static"></div>
        </div>
    </div>
</div>    
</div>
<script>
var toks = hex_sha512(" ");
$('[data-toggle="popover"]').popover();

$(".rgen").click(function(){
    var report_title = $(this).attr("report_title");
    $.ajax({
        url      : "<?=site_url('reports_/reportconfig')?>",
        type     : "POST",
        data     : {report   :  GibberishAES.enc(  "rfilter" , toks), rtype :  GibberishAES.enc($(this).attr("rtype") , toks), report_title :  GibberishAES.enc(report_title , toks), toks:toks},
        success: function(msg){
            $("#myModal").html(msg);
            $(".modal-title").text(report_title+" Report");
        }
    });  
});

$(".att-rgen").click(function(){
    $.ajax({
        url      : "<?=site_url('reports_/showAttendanceReport')?>",
        type     : "POST",
        data     : {filter : GibberishAES.enc($(this).attr("rtype") , toks), toks:toks},
        success: function(msg){
            $("#myModal").html(msg);
        }
    }); 
});

$(".facialDowntime").click(function(){
    var formdata = "form=facialDownTimePDF";
    encodedData = encodeURIComponent(window.btoa(formdata));
    openWindowWithPost("<?=site_url("forms/loadForm")?>", {formdata: encodedData});
});
</script>

