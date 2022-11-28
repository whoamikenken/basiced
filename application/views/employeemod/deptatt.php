<?php
    $CI =& get_instance();
    $CI->load->model('utils');
    $cdatefrom = date("Y-m-d");
    $cdateto = date("Y-m-d");
    $user = $this->session->userdata("username");
    list($employeedept,$campus) = $CI->utils->getEmpDept($user);

    $ishead = $CI->utils->getDeptHead('head',$employeedept);

    $head = "";

    if ($user == $ishead) {
        $head = $employeedept;
    }
?>
<style type="text/css">
   .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <input type="hidden" name="deptid" value="<?=$this->employee->getindividualdept($user)?>" />
        <input type="hidden" value="<?=($this->employee->getempteachingtype($user) ? "teaching" : "nonteaching")?>" />
        <div class="row" style="padding-bottom: 0px;">
            <div class="col-md-12" style="padding-left: 0px;padding-bottom: 0xp;">
                <div class="panel">
                    <div class="panel-heading" style="background-color: #0072c6;">
                        <h4><b>Department Attendance</b></h4>
                    </div>
                    <div class="panel-body"><br><br>
                        <form id="frm-filter">
                            <div class="col-md-12">
                                <div class="col-md-6">
                                    <input type="hidden" name="selected_campus" value="<?=$campus?>">
                                    <input type="hidden" name="selected_dept" value="<?=$employeedept?>">
                                    <input type="hidden" name="head" value="<?=$head?>">

                                    <div class="form_row no-search">
                                        <label class="field_name align_right">Cut-Off</label>
                                        <div class="field" style="padding-bottom: 10px;">
                                            <select class="chosen" id="cutoff" name="cutoff_date"><?=$this->employeemod->displayCutOff()?></select>
                                        </div>
                                    </div>

                                    <div class="form_row no-search">
                                        <label class="field_name align_right">Type</label>
                                        <div class="field" style="padding-bottom: 10px;">
                                            <select class="chosen" id="tnt" name="teaching_type">
                                                <?
                                                    $type = array(""=>"Select Teaching Type", "teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                                                        foreach($type as $c=>$val){
                                                        ?><option value="<?=$c?>"><?=$val?></option><?
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form_row no-search">
                                        <label class="field_name align_right">Employee</label>
                                        <div class="field" style="padding-bottom: 10px;">
                                            <select class="chosen" name="employeeid" id="employeeid">

                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form><br>
                        <div class="col-md-12" style="margin-left: 10.3%;margin-top: 1%;">
                            <button class="btn btn-primary" href="#" id="attendance_report"> Attendance Report</button>
                            <a href="#" class="btn btn-primary" id="individual_report">Individual Report</a>
                            <a href="#" class="btn btn-primary" id="print_attendance_report">Attendance Report (Print)</a>
                            <a href="#" class="btn btn-primary" id="displayAttendanceModal">Print Individual (Print)</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <div id="displaylogs" style="padding: 5px;"></div>

</div>


<div class="modal fade" id="att_report_filter" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-md">

        <div class="modal-content">
            <div class="modal-header">
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
                        <h4 class="media-heading" style="font-size: 18px !important; font-weight: bold;">Pinnacle Technologies Inc.</h4>
                        <p style="font-family:Avenir; margin-top: -1%; font-size: 16px !important; font-weight: 300 !important">D`Great</p>
                    </div>
                </div>
                <center><b><h3 tag="title" class="modal-title">Click Checkbox For Filtering</h3></b></center>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div tag='display'>
                        <div class="category" align="center">
                            <form id="frm-print">
                                <input type="hidden" name="cutoff_date">
                                <input type="hidden" name="teaching_type">
                                <input type="hidden" name="empid_list">
                                <input type="checkbox" name="category" value="department" checked /> Per Department
                                <input type="checkbox" name="category" value="sort" /> Sort Alphabetically
                                <br><br>
<!--                                 <div class="form_row" id="department">
                                    <div class="field col-md-7" align="center">
                                        <label>Department : </label>
                                        <select class=" dept_keys form-control" name="dept_keys">
                                            <option value="">All Department</option>
                                            <?php 
                                                $opt_department = $this->extras->showdepartment();
                                                foreach($opt_department as $c=>$val): ?>
                                                    <option value="<?=$c?>"><?=$val?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-success" id='generate_report'>Generate</button>
                <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
            </div>
        </div>
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
                        <h4 class="media-heading">Pinnacle Technologies Inc.</h4>
                        <p>D`Great</p>
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
                    <button type="button" class="btn btn-success" id='print_batch'>Generate</button>
                    <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
                </div>
            </div>

        </div>
    </div>
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<div id="loading_div" style="display: none;"><img src="<?=base_url()?>images/loading.gif"></div>
<script src="<?=base_url()?>js/attendance/head_attendance.js"></script>