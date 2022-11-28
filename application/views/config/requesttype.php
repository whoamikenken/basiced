<?php

 /**
 * @author Kennedy Hipolito
 * @copyright 2019
 */
 
?>
<style type="text/css">
    .form_row{
        padding-bottom: 10px;
    }

          .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content" style="padding-top: 45px;">
    <div class="widgets_area">
        <div class="panel">
           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Remark Setup</b></h4></div>
           <div class="panel-body" id="data_table">
            
            </div>
        </div>
    </div>   
</div>
<script>
   request_setup();

    function request_setup(){
        $.ajax({
            url: "<?= site_url('setup_/loadRequestSetup')?>",
            success:function(response){
                $("#data_table").html(response);
            }
        });
    }
</script>