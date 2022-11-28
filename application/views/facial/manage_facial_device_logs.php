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
    var from = parseInt((new Date($("#from").val()+ " 01:00 AM").getTime() / 1000).toFixed(0));
    var to = parseInt((new Date($("#to").val()+ " 11:59 PM").getTime() / 1000).toFixed(0));
    swal.fire({
        html: '<h4>Processing.....</h4>',
        onRender: function() {
            $('.swal2-content').prepend(sweet_loader);
        }
    });

    if(from != "" && to != ""){

        $.ajax({
            type: "POST",
            url: "<?= site_url('facial_/getEmployeeWithNoLogs')?>",
            data: {serial_number:$("#deviceKey").val(),from:$("#from").val(),to:$("#to").val(), emp:$("#employeeid").val()},
            success:function(response){
                Swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Please wait for all the task to finish',
                    timer: 1500
                })
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
        url: "<?= site_url('facial_/loadEmployeeAll') ?>",
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