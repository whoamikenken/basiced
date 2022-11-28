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
           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Facial Registration</b></h4></div>
           <div class="panel-body">
            <div class="col-md-12">
                    <div class="col-md-6">
                        <div class="form-group" style="width: 70%;padding-left: 10px;">
                            <label class="field_name align_right">Facial Device</label>
                            <div class="field">
                                <select class="chosen" id="facial" name="facial">
                                    <?=$this->extras->getFacial()?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><br>
        <div id="table">
            
        </div>
    </div>   
</div>

<script>

    $("#facial").change(function(){
        var gate = $("#facial").val();
        facial_device(gate);
    });

    $(".chosen").chosen();

    function facial_device(gate){
        $.ajax({
            url: "<?= site_url('setup_/loadDevicePerson')?>",
            type: "POST",
            data: {gate:gate},
            success:function(response){
                $("#table").html(response);
            }
        });
    }
</script>