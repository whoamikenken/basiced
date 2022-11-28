<?php

 /**
 * @author Kennedy Hipolito
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
<div id="content" style="padding-top: 45px;">
    <div class="widgets_area">
        <div class="panel animated fadeIn">
           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Facial Device</b></h4></div>
           <div class="panel-body" id="data_table">
            
            </div>
        </div>
        <div class="panel animated fadeIn" hidden="">
           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Device History</b></h4></div>
           <div class="panel-body" id="history_table">
            
            </div>
        </div>
    </div>   
</div>

<!-- Modal -->
<div id="myModal" class="modal fade" role="dialog">

</div>

<script>
    loadFacialSetup();

    function loadFacialSetup(){
        $.ajax({
            url: "<?= site_url('setup_/loadFacialSetup')?>",
            success:function(response){
                $("#data_table").html(response);
            }
        });
    }
</script>