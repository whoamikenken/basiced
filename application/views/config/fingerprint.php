<?php

 /**
 * @author Kennedy Hipolito
 * @copyright 2019
 */
?>

<div id="content">
    <div class="widgets_area">
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading"><h4><b>Finger Print Registration</b></h4></div>
            <div class="panel-body">
                <div class="col-md-12" id="tableEmp">

                </div>                                                                      
            </div>
        </div>
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading"><h4><b>Finger Print Testing</b></h4></div>
            <div class="panel-body">
                <div class="col-md-12" id="ResgiteredEmp">

                </div>                                                                      
            </div>
        </div>
        <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading"><h4><b>Exclude Finger Print</b></h4></div>
            <div class="panel-body">
                <div class="col-md-12" id="ExcludeFP">

                </div>                                                                      
            </div>
        </div>
<!--         <div class="panel animated fadeIn delay-1s">
            <div class="panel-heading"><h4><b>Frequency Setup</b></h4></div>
            <div class="panel-body">
                <div class="col-md-12" id="Frequency">

                </div>                                                                      
            </div>
        </div> -->
    </div>   
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script>
$(document).ready(function(){
setTimeout(function(){ $('.panel').removeClass("animated fadeIn delay-1s");}, 3000);
});
getEmployeeList();
getEmployeeBio();
getEmployeeExcluded();

function getEmployeeList(){
    $.ajax({
        url:  $("#site_url").val() + "/setup_/getEmployeeForBio",
        type: "POST",
        data:{},
        success:function(response){
            $("#tableEmp").html(response);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#tableEmp").find(".btn").css("pointer-events", "none");
            else $("#tableEmp").find(".btn").css("pointer-events", "");
        }
    });
}

function getEmployeeBio(){
    $.ajax({
        url:  $("#site_url").val() + "/setup_/getEmployeeWithBio",
        type: "POST",
        data:{},
        success:function(response){
            $("#ResgiteredEmp").html(response);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#ResgiteredEmp").find(".btn").css("pointer-events", "none");
            else $("#ResgiteredEmp").find(".btn").css("pointer-events", "");
        }
    });
}

function getEmployeeExcluded(){
    $.ajax({
        url:  $("#site_url").val() + "/setup_/getEmployeeExcluded",
        type: "POST",
        data:{},
        success:function(response){
            $("#ExcludeFP").html(response);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#ExcludeFP").find(".btn").css("pointer-events", "none");
            else $("#ExcludeFP").find(".btn").css("pointer-events", "");
        }
    });
}

</script>