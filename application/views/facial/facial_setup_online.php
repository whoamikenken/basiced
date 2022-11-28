<?php

 /**
 * @author Kennedy Hipolito
 * @copyright 2019
 */

?>
<div id="content" style="padding-top: 45px;">
    <a href="#" type="button" id="backTomanage" class="btn btn-primary" style="margin-left: 1.2%; display: none">Back to Facial Devices</a>
    <div class="widgets_area">
        <div class="panel animated fadeIn">
           <div class="panel-heading" id="setupTable"><h4><b>Facial Devices Online</b></h4></div>
           <div class="panel-body" id="data_table">
            
            </div>
        </div><br>
        <div id="tableLogs" style="display: none">
            
        </div>
    </div>   
</div>

</div>

<script>
    loadFacialSetup();

    function loadFacialSetup(){
        $.ajax({
            url: "<?= site_url('facial_/loadFacialSetup')?>",
            success:function(response){
                $("#data_table").html(response);
                $("#backTomanage").css("display", "none");
            }
        });
    }

    $("#backTomanage").click(function(){
        $("#setupTable").html("<h4><b>Facial Devices Online</b></h4>");
        loadFacialSetup();
    });
</script>