<?php 
/**
 * @Kennedy Hipolito
 * @2019
 * @Updated UI
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
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Philhealth Share Percentage List</b></h4></div>
            <div class="panel-body" id="data_table">
            </div>
        </div>
    </div>
</div>
<script>

empshare_setup();

function empshare_setup(){
    $.ajax({
            url: "<?= site_url('setup_/loadPhilhealthShareSetup')?>",
            success:function(response){
                $("#data_table").html(response);
            }
    });
}

</script>