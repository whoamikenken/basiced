<link href="<?=base_url();?>css/attendance/attendance.css" rel="stylesheet">

<style type="text/css">
    .form_row{
        padding-bottom: 10px;
    }

    .panel-body{
        margin-top: 10px;        
    }
    .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>

<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Cut-Off</b></h4></div>
                    <div class="panel-body">
                        <div class="form_row" style="padding-bottom: 5px;">
                            <label class="field_name align_left"> Select Category</label>
                            <div style="width: 30%;">
                                <select class="chosen" id="category">
                                    <option value="" >- Category -</option>
                                    <option value="Message" >Cut-Off Config</option>
                                    <option value="processdtr" >Process DTR</option>
                                </select>
                            </div>
                        </div>
                        <a class="btn btn-primary btnsearch" href="javascript:reloaddata()"><i class="icon icon-refresh"> Search</i></a>
                        <span style="float: right;display: none;" id="notif_loading"><img src='<?=base_url()?>images/loading.gif'/> Notifying employee attendance confirmation ... </span>
                    </div>
                </div>
            </div>
        </div>
        <div id="contents"></div>
    </div>    
</div>   
<div id="loading"><img src='<?=base_url()?>images/loading.gif'/> Loading.. Please wait...</div>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<input type="hidden" id="username" value="<?= $this->session->userdata('username') ?>">

<script src="<?=base_url()?>js/attendance/emp_attendance_cutoff.js"></script>
