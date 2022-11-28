<?php

/**
 * @author Kennedy Hipolito
 * @2019
 * @Updated UI
 */

?>
<input type="hidden" name="ip" id="ip" value="<?= $ip ?>">
<form>
    <div class="col-md-12">
        <div class="form-group">
            <label for="serial_number">Device I.D</label>
            <input class="form-control" id="serial_number" name="serial_number" type="text" value="<?= isset($deviceKey) ? $deviceKey : ""?>" disabled/>
        </div>
        <div class="form-group">
            <label for="recogDistance">Campus</label>
            <select class="form chosen form-control" name="campusid" id="campusid"><?=$this->extras->getCampuses(isset($campusid) ? $campusid : "")?></select>
        </div>
        <div class="form-group">
            <label for="name">Name</label>
            <input class="form-control" id="name" name="name" type="text" value="<?= isset($name) ? $name : ""?>"/>
        </div>
        <div class="form-group">
            <label for="recog">Recognition Prompt</label>
            <input class="form-control" id="recog" name="recog" type="text" value="<?= isset($recog) ? $recog : ""?>"/>
        </div>
        <div class="form-group">
            <label for="recogScore">Recognition Score</label>
            <input class="form-control" id="recogScore" name="recogScore" type="text" value="<?= isset($recogScore) ? $recogScore : "70"?>" min="1" max="100"/>
        </div>
        <div class="form-group">
            <label for="recogDistance">Recognition Distance</label>
            <select class="form-control" id="recogDistance" data-placeholder="Select" deviceKey="">
                <option <?= isset($recogDistance) ? ($recogDistance == 0)? "selected":"" : ""?> value="0">Unlimited</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 1)? "selected":"" : ""?> value="1">0.5 meter</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 2)? "selected":"" : ""?> value="2">1 meter</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 3)? "selected":"" : ""?> value="3">1.5 meter</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 4)? "selected":"" : ""?> value="4">2 meters</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 5)? "selected":"" : ""?> value="5">3 meters</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 6)? "selected":"" : ""?> value="6">4 meters</option>
            </select>
        </div>
        <div class="form-group">
            <label for="recogDistance">Recognition Interval</label>
            <input type="number" class="form-control" name="recogInterval" max="180" id="recogInterval" value="<?= isset($recogInterval) ? $recogInterval : 60?>">
        </div>
        <div class="form-group">
            <label for="file">File input</label>
            <input type="file" id="file">
        </div>
        <div class="form-group" style="text-align: center;">
            <?php if ($filetype != "") { ?>
            <label for="exampleInputFile">Logo Image</label><br>
            <img id="profile-picture" class="img-thumbnail" src="data:<?= $filetype ?>;base64,<?= $image ?>" width="230" height="230">
            <?php } ?>
        </div>
    </div>
</form>
<script>

$("#button_save_modal").unbind('click').bind('click', function(event) {
    var serial_number = $("#serial_number").val();
    var ip = $("#ip").val();
    var name = $("#name").val();
    var recog = $("#recog").val();
    var campusid = $("#campusid").val();
    var file = $('#file')[0].files[0];
    var distance   =   $("#recogDistance").val();
    var recogScore   =   $("#recogScore").val();
    var recogInterval   =   $("#recogInterval").val();
    var fd = new FormData();
    if (file != "") {
        fd.append('file', file);
    }
    fd.append('name', name);
    fd.append('recog', recog);
    fd.append('recogDistance', distance);
    fd.append('recogScore', recogScore);
    fd.append('recogInterval', recogInterval);
    fd.append('serial_number', serial_number);
    fd.append('campusid', campusid);

    if(serial_number != "" && name != "" && recog != ""){
        $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/saveFacialSetup')?>",
        data: fd,
        dataType:"JSON",
        processData: false,  // tell jQuery not to process the data
        contentType: false,  // tell jQuery not to set contentType
        success:function(response){
            if (response.err_code == 1) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Data Successfully Updated.',
                    showConfirmButton: true,
                    timer: 1500
                })
                if (response.base64 != "") {
                    var taskData = '"pass":"12345678","imgBase64":"'+ response.base64 +'"';
                    var interface = 'changeLogo';
                    $.ajax({
                        type: "POST",
                        url: "<?= site_url('facial_/saveTaskToDevice')?>",
                        data: {serial_number:serial_number,interface:interface,task:taskData,ip:ip},
                        success:function(response){

                        }
                    });
                }
                var taskData = '"pass":"12345678","config":{"comModContent":"#34WG{id}#","delayTimeForCloseDoor":500,"recStrangerTimesThreshold":5,"recRank":3,"multiplayerDetection":2,"ttsModStrangerContent":"Sorry You are Not Registered","ttsModStrangerType":100,"recStrangerType":2,"displayModContent":"{name}","displayModType":1,"ttsModContent":"'+recog+'","ttsModType":100,"wg":"#WG{idcardNum}#","intro":"'+recog+'","slogan":" SLOGAN","comModType":100,"saveIdentifyTime":'+recogInterval+',"identifyScores":"'+recogScore+'","identifyDistance":"'+distance+'","companyName":"'+name+'"}';
                var interface = 'setConfig';
                $.ajax({
                    type: "POST",
                    url: "<?= site_url('facial_/saveTaskToDevice')?>",
                    data: {serial_number:serial_number,interface:interface,task:taskData},
                    success:function(response){

                    }
                });
            }else{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error Please Coordinate With Developer!',
                    timer: 1500
                })
            }
            loadFacialSetup();
            $('#modal-view').modal('toggle');
        }
        });
    }else{
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'All fields are required!',
            timer: 1500
        })
    }
});

</script>