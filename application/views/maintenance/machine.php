<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>

<link href="<?=base_url();?>css/terminal_setup/terminal.css" rel="stylesheet">
<div id="content"> <!-- Content start -->
    <a id="addnewterminal" data-toggle="modal" href="#dtr-modal" class="btn btn-primary" style="margin-top: 20px;margin-left: 20px;"><i class="icon-plus-sign"></i> Add New</a><br><br>
    <div class="widgets_area" >
        <div class="row" >
            <div id="gateuserlist">
                <div class="panel animated fadeIn" >
                    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>User List</b></h4>
                    </div>
                    <div class="panel-body" style="padding-top: 0px;">
                        <input type="hidden" id="site_url" value="<?= site_url(); ?>">
                        <div class="well-content" id="gate_user_list"></div>
                    </div>
                </div>
            </div>
            <div id="gateuserhistory">
                <div class="panel animated fadeIn">
                    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Gate History</b></h4>
                    </div>
                    <div class="panel-body" id="gate_history"></div>
                </div>
            </div>
        </div>
    </div>    
</div> 

<!-- Modal -->
<div class="modal fade" id="manage_machine" role="dialog"></div>

<script src="<?=base_url()?>js/terminal_setup/terminal.js"></script>