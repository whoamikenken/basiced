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
</style>
<div id="content" style="padding-top: 10px;">

    <div class="widgets_area">
        <div class="panel">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Manage Rank</b></h4></div>
            <div class="panel-body">
                <div class="col-md-12" id="data_table">
                
                </div>
            </div>
        </div>
    </div>
    
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">

</div>

<script>
    managerank_setup();

    function managerank_setup(){
        $.ajax({
               type: "POST",
                url: "<?= site_url('setup_/loadManageRankSetup')?>",
                success:function(response){
                    $("#data_table").html(response);
                }
        });
    }
</script>