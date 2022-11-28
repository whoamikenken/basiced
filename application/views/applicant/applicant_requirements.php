<?php

 /**
 * @author Max Consul
 * @copyright 2019
 */
 
?>
<style type="text/css">
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;   
}

    .modal-overflow .modal-body{
        margin-bottom: 0px;

    }
</style>
<div id="content" style="padding-top: 40px;">
    <div class="widgets_area">
        <div class="panel">
           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Initial Requirements</b></h4></div>
           <div class="panel-body" id="data_table_init">
            
            </div>
        </div>

        <div class="panel">
           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Pre Employment Requirements</b></h4></div>
           <div class="panel-body" id="data_table_pre">
            
            </div>
        </div>
    </div>   
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">

</div>

<script>
    initRequirements_setup();
    preRequirements_setup();

    function initRequirements_setup(){
        $.ajax({
            url: "<?= site_url('setup_/loadInitialRequirementsSetup')?>",
            success:function(response){
                $("#data_table_init").html(response);
            }
        });
    }

    function preRequirements_setup(){
        $.ajax({
            url: "<?= site_url('setup_/loadPreRequirementsSetup')?>",
            success:function(response){
                $("#data_table_pre").html(response);
            }
        });
    }

</script>