<?php

 /**
 * @author Kennedy Hipolito
 * @copyright 2019
 */
?>

<div id="content">
    <div class="widgets_area">
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading"><h4><b>Finger Print Verification</b></h4></div>
            <div class="panel-body">
                <div class="col-md-12" id="ResgiteredEmp">

                </div>                                                                      
            </div>
        </div>
    </div>   
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script>
$(document).ready(function(){
    setTimeout(function(){ $('.panel').removeClass("animated fadeIn delay-1s");}, 3000);
});

getEmployeeBio();

function getEmployeeBio(){
    $.ajax({
        url:  $("#site_url").val() + "/fingerprint_/getEmployeeWithBio",
        type: "POST",
        data:{},
        success:function(response){
            $("#ResgiteredEmp").html(response);
        }
    });
}

</script>