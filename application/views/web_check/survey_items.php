<?php

/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */

?>

<div id="content">
    <div class="widgets_area">
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading"><h4><b>Survey Items Setup</b></h4></div>
            <div class="panel-body">
                <div class="col-md-12" id="table">

                </div>                                                                      
            </div>
        </div>
    </div>   
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script>
$(document).ready(function(){
    surveyItemsSetup();
    setTimeout(function(){ $('.panel').removeClass("animated fadeIn delay-1s");}, 3000);
});


function surveyItemsSetup(){
    $.ajax({
        url:  $("#site_url").val() + "/webcheckin_/loadSurveyItemsSetupTable",
        type: "POST",
        success:function(response){
            $("#table").html(response);
        }
    });
}


</script>