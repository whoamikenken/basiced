<?php $datetoday = date("Y-m-d"); ?>
<style type="text/css">
   .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12" >
                <div class="panel animated fadeIn">
                    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Professional Development</b></h4></div>
                    <div class="panel-body">
                        <div class="col-md-12" style="padding-left: 0px;">
                            <div class="col-md-12">
                                <!-- <label class="field_name" class="col-md-2" style="float: left;">Date</label>
                                <div class="col-md-5" style="width: 15%;">
                                    <div class='input-group date' id='datetimepicker1' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="datesetfrom" value="<?=$datetoday?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                                <div class="col-md-5" style="width: 15%;">
                                    <div class='input-group date' id='datesetto' data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="datesetto" value="<?=$datetoday?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div> -->
                            </div>
                            <div class="col-md-12" style="padding-left: 0px;">
                               <!--  <a href="#" class="btn btn-primary" id="search">Search</a>&nbsp;&nbsp;&nbsp; -->
                                <a class="btn btn-primary" id="newrequest" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default">New Request</a>
                            </div>  
                            <br>
                            <div class="panel-body" id="seminarhistory" style="padding: 0px;"></div>
                        </div>
                </div>
            </div>        
        </div>        
        </div>
<!--         <div class="panel" style="margin: 10px;">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>History</b></h4></div>
            
        </div> -->
    </div>
</div>

<div class="modal fade" id="myModal" data-backdrop="static"></div>
<div id="loading_div"><img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..</div>
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script src="<?=base_url()?>js/seminar/seminar.js"></script>