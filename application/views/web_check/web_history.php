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
            <div class="panel-heading"><h4><b>Web Check-In History</b></h4></div>
            <div class="panel-body">
                <div class="row">
                    <form id="webHistoryFilter">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="exampleInputName2">Status</label>
                            <select class="chosen form-control" name="status">
                                <option value="">All Status</option>
                                <option value="1">Active</option>
                                <option value="0">In-Active</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="exampleInputName2">Type</label>
                            <select class="chosen form-control" name="type" id="type">
                                <option value="">All Type</option>
                                <option value="teaching">Teaching</option>
                                <option value="nonteaching">Non-Teaching</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="form-group" style="margin-bottom: 0px;">
                            <label for="exampleInputName2">Webcheckin Date</label> 
                        </div>
                        <div class="col-md-1" style="padding-left: 0px;">From</div>
                        <div class="col-md-5">
                            <div class='input-group date' id="ldfrom" data-date-format="yyyy-mm-dd">
                                <input type='text' class="form-control" size="16" name="from" type="text" value=""/>
                                <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-1">To</div>
                        <div class="col-md-5">
                            <div class='input-group date' data-date="" data-date-format="yyyy-mm-dd">
                                <input type='text' class="form-control" size="16" name="to" type="text" value="" id="ldto" />
                                <span class="input-group-addon">
                                      <span class="glyphicon glyphicon-calendar"></span>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="exampleInputName2">Department</label>
                            <select class="form chosen form-control" name="deptid" id="deptid"><?=$this->extras->getDeptpartment()?></select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="exampleInputName2">Office</label>
                            <select class="form chosen" name="office" id="office"><?=$this->extras->getOffice()?></select>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="form-group">
                            <label for="exampleInputName2">Employee</label>
                            <select class="chosen form-control" name="employeeFilter" id="employeeFilter">
                                <option value="">All Employee</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group" style="margin-top: 25px;">
                            <button class="btn btn-success" id="searchHistory" type="button" >Search</button>
                        </div>
                    </div>
                    </form>
                </div><br><br>
                <div class="col-md-12" id="table">

                </div>                                                                      
            </div>
        </div>
        <div class="col-md-12" id="checkInDetails" style="padding: 0px;">

        </div> 
    </div>   
</div>

<input type="hidden" id="site_url" value="<?= site_url() ?>">
<script>
var toks = hex_sha512(" ");
$(document).ready(function(){
    employeeList();
    setTimeout(function(){ $('.panel').removeClass("animated fadeIn delay-1s");}, 3000);
});

$("#searchHistory").click(function(){
    webHistory();
});

function webHistory(){
    var formdata = $("#webHistoryFilter").serialize();
    $.ajax({
        url:  $("#site_url").val() + "/webcheckin_/loadWebHistoryTable",
        type: "POST",
        data: {formdata: GibberishAES.enc(formdata, toks), toks:toks},
        success:function(response){
            $("#table").html(response);
        }
    });
}

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$(".chosen").chosen();

$(".chosen-employee").chosen();

// $(".chosen").change(function(){
//     webHistory();
//     employeeList();
// });

// $(".chosen-employee").change(function(){
//     webHistory();
// });

// $("#ldto").blur(function(){
//     webHistory();
// });


$("#type").change(function(){
    employeeList();
});

$("#deptid").change(function(){
    $.ajax({
        url : $("#site_url").val() + "/setup_/getOffice",
        type: "POST",
        data: {department:$(this).val()},
        success: function(msg){
            $("#office").html(msg).trigger("chosen:updated");
        }
    });
});


function employeeList(){
    $.ajax({
        url: $("#site_url").val() + "/webcheckin_/loadEmployeeListDropdownm",
        type: "POST",
        data:{type:$("#type").val(), deptid:$("#deptid").val(), office:$("#office").val() },
        success:function(response){
            $("#employeeFilter").html(response);
            $("#employeeFilter").trigger("chosen:updated");
        }
    });
}


</script>