<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
$today = date('Y-m-d g:i:s');
?>
<input type="hidden" name="deviceKey" id="deviceKey" value="<?= $deviceKey ?>">
<!-- <button id="load">Load</button> -->
<a class="btn btn-primary addperson" action="add" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-edit"></i>&nbsp;&nbsp;Add Employee</a><br><br>
<a class="btn btn-primary pritFacialUser"><i class="glyphicon glyphicon-print"></i>&nbsp;&nbsp;Print Users</a><br><br>
<form id="logDeviceFilter">
    <div class="row">
        <div class="col-md-2">
            <div class="form-group">
                <label for="exampleInputName2">Status</label>
                <select class="chosen form-control" name="status" id="status">
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
        <div class="col-md-2">
            <div class="form-group">
                <label for="exampleInputName2">Department</label>
                <select class="chosen-employee" name="deptid" id="deptid"><?=$this->extras->getDeptpartment()?></select>
            </div>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <label for="exampleInputName2">Office</label>
                <select class="chosen-employee" name="office" id="office"><?=$this->extras->getOffice()?></select>
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
    </div>
</form>
<div id="personTableDiv">
    
</div>

<script>
var toks = hex_sha512(" ");
var serial_number = $("#deviceKey").val();

loadFacialSetupPerson();

function loadFacialSetupPerson(){
    console.log(serial_number);
    $("#backTomanage").css("display", "unset");
    $.ajax({
        url: "<?= site_url('facial_/loadFacialDevicePersonTable')?>",
        type: "POST",
        data:{code: serial_number,type:$("#type").val(), deptid: $("#deptid").val(), office: $("#office").val(), status: $("#status").val(), empid: $("#employeeFilter").val()},
        success:function(response){
            $("#personTableDiv").html(response);
        }
    });
}

function employeeList(){
    $.ajax({
        url: $("#site_url").val() + "/webcheckin_/loadEmployeeListDropdownm",
        type: "POST",
        data:{type:GibberishAES.enc($("#type").val() , toks), deptid: GibberishAES.enc($("#deptid").val(), toks), office: GibberishAES.enc($("#office").val(), toks), toks:toks },
        success:function(response){
            $("#employeeFilter").html(response);
            $("#employeeFilter").trigger("chosen:updated");
        }
    });
}

$(".chosen").change(function(){
    $("#facial_Log_table").DataTable().draw();
    employeeList();
});

$(".chosen-employee").change(function(){
    employeeList();
    loadFacialSetupPerson();
});

$(".chosen").change(function(){
    loadFacialSetupPerson();
});

$(".chosen").chosen();
$(".chosen-employee").chosen();


$(".addperson").click(function(){
    $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/managePerson')?>",
        data: {code:serial_number,action:"Added"},
        success:function(response){
            $("#modal-view").find(".modal-title").html("Add Person To Device");
            $("#modal-view").find("div[tag='display']").html(response);
            
        }
    });
});


$(".pritFacialUser").click(function(){
    openWindowWithPost("<?=site_url("facial_/printFacialReport")?>", {serial: serial_number, form:"facialUserList"});
});

</script>