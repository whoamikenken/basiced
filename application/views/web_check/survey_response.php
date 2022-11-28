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
            <div class="panel-heading"><h4><b>Survey Responses</b></h4></div>
            <div class="panel-body">
                <div class="row">
                    <form id="reponseHistoryFilter">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Category</label>
                            <select class="chosen" name="category">
                                <?=$this->extras->getSurveyCategory()?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Department</label>
                            <select class="chosen empSort" name="deptid">
                                <?=$this->extras->getDeptpartment()?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Survey Description</label>
                            <select class="chosen" name="survey">
                                <?=$this->extras->getSurveyDescription()?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Office</label>
                            <select class="chosen empSort" id="office" name="office">
                                <?=$this->extras->getOffice()?>
                            </select>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Type</label>
                            <select class="chosen empSort" name="type" id="type">
                                <option value="">All Type</option>
                                <option value="teaching">Teaching</option>
                                <option value="nonteaching">Non-Teaching</option>
                            </select>
                        </div>
                    </div>
                    <input type="hidden" name="selected" value="itemsAll">
                    <input type="hidden" name="employeeFilter" id="employeeSelect" value="all">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label>Employee</label>
                            <select class="chosen" isFilter="filter" id="employeeFilter" multiple="multiple">
                            </select>
                        </div>
                    </div>
                    </form>
                </div>
                <div class="row" style="text-align: center;">
                    <div class="col-md-4">
                        <button id="PrintResponse" class="btn btn-info">Print List Of Responses</button>
                    </div>
                    <div class="col-md-4">
                        <button id="PrintResponseForm" class="btn btn-info">Print Form Responses</button>
                    </div>
                    <div class="col-md-4">
                        <button id="PrintResponseSummary" class="btn btn-info">Print Summary</button>
                    </div>
                </div><br><br>
                <div class="col-md-12" id="table">

                </div>                                                                      
            </div>
        </div>
    </div>   
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.min.js" integrity="sha512-s+xg36jbIujB2S2VKfpGmlC3T5V2TF3lY48DX7u2r9XzGzgPsa6wTpOQA7J9iffvdeBN0q9tKzRxVxw1JviZPg==" crossorigin="anonymous"></script>
<script src="<?=base_url()?>js/html2canvas.js"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/jspdf/1.3.3/jspdf.min.js"></script>
<script>
var oldSelect;
$(document).ready(function(){
    employeeList();
    responseHistory();
    setTimeout(function(){ $('.panel').removeClass("animated fadeIn delay-1s");}, 3000);
});


function responseHistory(){
    var formdata = $("#reponseHistoryFilter").serialize();
    $.ajax({
        url:  $("#site_url").val() + "/webcheckin_/loadResponseHistoryTable",
        type: "POST",
        data: formdata,
        success:function(response){
            $("#table").html(response);
        }
    });
}

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$(".chosen").chosen();

$(".chosen").change(function(){
    var list = $(this).val();
    if (list === undefined || list == null) {
        $("#employeeSelect").val("all");
    }else{
        $("#employeeSelect").val(list.toString());
    }
    responseHistory();
});

$(".empSort").change(function(){
    employeeList();
});

$("#PrintResponse").click(function(){
    var formdata = $("#reponseHistoryFilter").serialize();
    formdata +=  "&form=surveyRespondee";
    window.open("<?=site_url("forms/loadForm")?>?"+formdata,"");
});

$("#PrintResponseForm").click(function(){
    var formdata = $("#reponseHistoryFilter").serialize();
    formdata +=  "&form=resultResponder";
    window.open("<?=site_url("forms/loadForm")?>?"+formdata,"");
});

function employeeList(){
    var formdata = $("#reponseHistoryFilter").serialize();
    $.ajax({
        url: $("#site_url").val() + "/webcheckin_/loadEmployeeListDropdownm",
        type: "POST",
        data: formdata,
        success:function(response){
            $("#employeeFilter").html(response);
            $("#employeeFilter").trigger("chosen:updated");
        }
    });
}

$("#PrintResponseSummary").click(function(){  
        var formdata = $("#reponseHistoryFilter").serialize();
        $.ajax({
            url:  $("#site_url").val() + "/webcheckin_/getSurveySummary",
            type: "POST",
            data: formdata,
            success:function(response){
                $("#table").html(response);
            }
        });
    });

</script>