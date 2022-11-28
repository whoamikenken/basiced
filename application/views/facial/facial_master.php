<?php

 /**
 * @author Kennedy Hipolito
 * @copyright 2019
 */
 
?>
<div id="content" style="padding-top: 45px;">
    <div class="widgets_area">
        <button class="btn btn-primary" id="DeviceLogs"><i class="glyphicon glyphicon-cog"></i>&nbsp;&nbsp; View Facial Device Logs</button>&nbsp;&nbsp;&nbsp;&nbsp;
        <button class="btn btn-primary" id="StrangerLogs"><i class="glyphicon glyphicon-cog"></i>&nbsp;&nbsp; View Stranger Logs</button><br><br>
        <div class="panel animated fadeIn">
            <div class="panel-heading"><h4><b>Facial Devices Logs</b></h4></div>
            <div class="panel-body">
                <div id="logDeviceFilter">
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
                        <div class="col-md-8">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <label for="exampleInputName2">Facial Log Date</label> 
                            </div>
                            <div class="col-md-1" style="padding-left: 0px;">From</div>
                            <div class="col-md-5">
                                <div class='input-group date' data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="from" type="text" id="ldfrom" value=""/>
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
                                <label for="exampleInputName2">Devices</label>
                                <select class="form chosen form-control" name="device" id="device"><?=$this->extras->getDevices()?></select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="exampleInputName2">Department</label>
                                <select class="form chosen form-control" name="deptid" id="deptid"><?=$this->extras->getDeptpartment()?></select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="exampleInputName2">Office</label>
                                <select class="form chosen-employee" name="office" id="office"><?=$this->extras->getOffice()?></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="exampleInputName2">Employee</label>
                                <select class="chosen-employee form-control" name="employeeFilter" id="employeeFilter">
                                    <option value="">All Employee</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="exampleInputName2">Employment Status</label>
                                <select class="chosen-employee form-control" name="empstatFilter" id="empstatFilter">
                                    <?php
                                      $empstatuses = $this->extras->showemployeestatus('All Employment Status');
                                      foreach ($empstatuses as $key => $item) {
                                        ?>
                                        <option value='<?=$key?>'><?= ucfirst (strtolower ($item)); ?></option>
                                        <?php
                                      }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        
                        <div class="col-md-12">
                            <div class="form-group" style="text-align: center; float: right;">
                                <button id="searchLogs" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <div id="logDeviceFilterStranger" style="display: none">
                    <div class="row">
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="exampleInputName2">Devices</label>
                                <select class="form chosen form-control" name="device" id="deviceStranger"><?=$this->extras->getDevices()?></select>
                            </div>
                        </div>
                        <div class="col-md-8">
                            <div class="form-group" style="margin-bottom: 0px;">
                                <label for="exampleInputName2">Facial Log Date</label> 
                            </div>
                            <div class="col-md-1" style="padding-left: 0px;">From</div>
                            <div class="col-md-5">
                                <div class='input-group date' data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="from" type="text" id="ldfromStranger" value=""/>
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-1">To</div>
                            <div class="col-md-5">
                                <div class='input-group date' data-date="" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="to" type="text" value="" id="ldtoStranger" />
                                    <span class="input-group-addon">
                                    <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group" style="text-align: center;margin-top: 10%;">
                                <button id="searchLogsStranger" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </div>
                </div>
                <br><br>
                <div id="facial_Log">
                    <table class="table table-striped table-bordered table-hover" id="facial_Log_table">
                        <thead>
                            <tr>
                                <th><b>Employee ID</b></th>
                                <th><b>Name</b></th>
                                <th><b>Office</b></th>
                                <th><b>Date</b></th>
                                <th><b>Device Name</b></th>
                                <th><b>Device I.D</b></th>
                                <th><b>Image</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
                <div id="facial_Log_Strangers" style="display: none;">
                    <table class="table table-striped table-bordered table-hover" id="facial_Log_Strangers_table">
                        <thead>
                            <tr>
                                <th><b>Date</b></th>
                                <th><b>Device I.D</b></th>
                                <th><b>Image</b></th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>   
    </div>
</div>
<input type="hidden" id="site_url" value="<?= site_url() ?>">

<script>
    var first = 1;
    var firstStr = 1;
    var toks = hex_sha512(" ");
    employeeList();
    $(document).ready(function(){
        setTimeout(function(){ $('.panel').removeClass("animated fadeIn delay-1s");}, 3000);
    });

    $(".date").datetimepicker({
        format: "YYYY-MM-DD"
    });

    $(".chosen").chosen();

    $(".chosen-employee").chosen();

    $(".chosen").change(function(){
        employeeList();
    });

    $("#deptid").change(function(){
        officeList();
    });

    $("#searchLogs").click(function(){
        if (first == 1) {
            loadFacialLogs();
            first++;
        }else{
            $("#facial_Log_table").DataTable().draw();
        }
    });

    $("#searchLogsStranger").click(function(){
        if (firstStr == 1) {
            loadFacialLogsStranger();
            firstStr++;
        }else{
            $("#facial_Log_Strangers_table").DataTable().draw();
        }
    });

    $("#StrangerLogs").click(function(){
        $("#facial_Log_Strangers").show();
        $("#logDeviceFilterStranger").show();
        $("#logDeviceFilter").hide();
        $("#facial_Log").hide();
    });

    $("#DeviceLogs").click(function(){
        $("#facial_Log_Strangers").hide();
        $("#logDeviceFilterStranger").hide();
        $("#logDeviceFilter").show();
        $("#facial_Log").show();
    });

    function loadFacialLogs(){
        var logsTable = $("#facial_Log_table").DataTable({ 
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= site_url('facial_/getLogs')?>", 
                data: function (data) { 
                    data.type = GibberishAES.enc($("#type").val() , toks)
                    data.deptid = GibberishAES.enc($("#deptid").val(), toks)
                    data.office = GibberishAES.enc($("#office").val(), toks)
                    data.toks = toks
                    data.employee = GibberishAES.enc($("#employeeFilter").val(), toks)
                    data.empstat = GibberishAES.enc($("#empstatFilter").val(), toks)
                    data.from = GibberishAES.enc($("#ldfrom").val(), toks)
                    data.to = GibberishAES.enc($("#ldto").val(), toks)
                    data.status = GibberishAES.enc($("#status").val(), toks)
                    data.serial = GibberishAES.enc($("#device").val(), toks)
                    data.serialStranger = GibberishAES.enc($("#deviceStranger").val(), toks)
                    data.fromStranger = GibberishAES.enc($("#ldfromStranger").val(), toks)
                    data.toStranger = GibberishAES.enc($("#ldtoStranger").val(), toks)
                    data.tableType = "facial_Log"
                }  
            },
            deferRender: true,
            searching: true,
            columnDefs: [ { type: 'date', 'targets': [3] } ]
        });
    }

    function loadFacialLogsStranger(){
        var strangerTable = $("#facial_Log_Strangers_table").DataTable({ 
            "lengthMenu": [[10,15,20,25,-1], [10,15,20,25,'All']],
            processing: true,
            serverSide: true,
            ajax: {
                url: "<?= site_url('facial_/getLogsStrangers')?>", 
                data: function (data) { 
                    data.serialStranger = GibberishAES.enc($("#deviceStranger").val(), toks)
                    data.startdate = GibberishAES.enc($("#ldfromStranger").val(), toks)
                    data.enddate = GibberishAES.enc($("#ldtoStranger").val(), toks)
                    data.toks = toks
                }
            },
            deferRender: true,
            searching: true,
            pageLength: 5, 
            columnDefs: [ { type: 'date', 'targets': [0] } ]  
        });
    }

    function employeeList(){
        $.ajax({
            url: $("#site_url").val() + "/webcheckin_/loadEmployeeListDropdownm",
            type: "POST",
            data:{type:GibberishAES.enc($("#type").val() , toks), deptid: GibberishAES.enc($("#deptid").val(), toks), office: GibberishAES.enc($("#office").val(), toks), status: GibberishAES.enc($("#status").val(), toks), toks:toks },
            success:function(response){
                $("#employeeFilter").html(response);
                $("#employeeFilter").trigger("chosen:updated");
            }
        });
    }

    function officeList(){
        $.ajax({
            url: $("#site_url").val() + "/webcheckin_/loadOfficeByDept",
            type: "POST",
            data:{deptid: GibberishAES.enc($("#deptid").val(), toks), toks:toks },
            success:function(response){
                $("#office").html(response);
                $("#office").trigger("chosen:updated");
            }
        });
    }

</script>