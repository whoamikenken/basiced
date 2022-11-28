<?php

/**
 * @author Kennedy Hipolito
 * @2019
 * @Updated UI
 */

?>
<form id="FormDeviceSetting">
    <div class="col-md-12">
        <div class="form-group">
            <label for="deviceKey">Device I.D</label>
            <input class="form-control" id="deviceKey" name="deviceKey" type="text" value="<?= isset($deviceKey) ? $deviceKey : ""?>" style="pointer-events:none"/>
        </div>
        <div class="form-group">
            <label for="deviceName">Name</label>
            <input class="form-control" id="deviceName" name="deviceName" type="text" value="<?= isset($deviceName) ? $deviceName : ""?>"style="pointer-events:none"/>
        </div>
        <div class="form-group">
            <label for="deviceName">IP</label>
            <input class="form-control" id="ip" name="ip" type="text" value="<?= isset($ip) ? $ip : ""?>"style="pointer-events:none"/>
        </div>
        <div class="form-group">
            <label for="serial_number">Employee</label>
            <select class="chosen" id="employeeid">
            </select>
        </div>
        <div class="form-group">
            <label for="deviceName">From</label>
            <div class='input-group date' data-date-format="yyyy-mm-dd">
                <input type='text' class="form-control" size="16" name="from" id="from" type="text" value=""/>
                <span class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
        <div class="form-group">
            <label for="deviceName">To</label>
            <div class='input-group date'  data-date-format="yyyy-mm-dd">
                <input type='text' class="form-control" size="16" name="to" id="to" type="text" value=""/>
                <span class="input-group-addon">
                      <span class="glyphicon glyphicon-calendar"></span>
                </span>
            </div>
        </div>
    </div>
</form>
<script>

loadEmp();

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$("#button_save_modal").unbind('click').bind('click', function(event) {
    var from = $("#from").val()+ " 01:00:00";
    var to = $("#to").val()+ " 23:59:00";
    swal.fire({
        html: '<h4>Processing.....</h4>',
        onRender: function() {
            $('.swal2-content').prepend(sweet_loader);
        }
    });

    if(from != "" && to != ""){
        var ip = $("#ip").val();
        code = $(this).attr('code');
        ip = "<?php echo $ip ?>";
        var personId = "-1";
        if($("#employeeid").val() == "all"){
            personId = $("#employeeid").val();
        }
        $.ajax({
            type: "POST",
            crossDomain: true,
            url: "http://"+ip+":8090/findRecords",
            dataType: "JSON",
            data: {
                "pass": "12345678",
                    "personId": personId,
                    "length": "-1",
                    "index": "0",
                    "startTime": from,
                    "endTime": to,
                    "model": "0"
            },
            success:function(response){
                var record = [];
                for (const element of response.data.records) { // You can use `let` instead of `const` if you like
                    
                    if(element.state == 0 && element.personId != "STRANGERBABY"){
                        $.ajax({
                            url: "http://localhost:8098/api/converterFTP",
                            type: "POST",
                            contentType: 'application/json',
                            data: JSON.stringify({"link": element.path,"base64": "string"}),
                            async: false,
                            success: function(msg) {
                                var obj = {
                                    'personId': element.personId,
                                    'time': element.time,
                                    'deviceKey': $("#deviceKey").val(),
                                    'type': element.type,
                                    'base64image': msg.base64,
                                }
                                $.ajax({
                                    url: "<?= site_url('facial_/syncLogsLocal')?>",
                                    type: "POST",
                                    dataType: 'json',
                                    async:false,
                                    data:JSON.stringify(obj),
                                    success:function(res){
                                        console.log(res);
                                    }
                                });
                            }
                        });
                    }
                }

                $.ajax({
                    url: "<?= site_url('facial_/reprocessFacialLogs')?>",
                    type: "POST",
                    async:false,
                    data:{from:$("#from").val(),to:$("#to").val()},
                    success:function(res){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success',
                            text: 'Resync success'
                        })
                    }
                });

            }
        });
    }else{
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'All fields are required',
            timer: 1500
        })
    }
});


function loadEmp(emp){
    $.ajax({
        url: "<?= site_url('facial_/loadDevicePersonEmployee') ?>",
        type: "POST",
        data: {
            deviceKey:$("#deviceKey").val()
        },
        success:function(response){
            $("#employeeid").html(response).trigger("chosen:updated");
        }
    });
}

$(".chosen").chosen();
</script>