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
            <input class="form-control" id="recog" name="recog" type="text" value="<?= isset($recog) ? $recog : "Welcome {name}"?>"/>
        </div>
        <div class="form-group">
            <label for="recogScore">Recognition Score</label>
            <input class="form-control" id="recogScore" name="recogScore" type="number" value="<?= isset($recogScore) ? $recogScore : "70"?>" min="1" max="100"/>
        </div>
        <div class="form-group">
            <label for="recogDistance">Recognition Distance</label>
            <select class="form-control chosen" id="recogDistance" data-placeholder="Select" deviceKey="">
                <option <?= isset($recogDistance) ? ($recogDistance == 0)? "selected":"" : ""?> value="0">Unlimited</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 1)? "selected":"" : ""?> value="1">0.5 meter</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 2)? "selected":"" : ""?> value="2">1 meter</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 3)? "selected":"" : ""?> value="3">1.5 meter</option>
                <option <?= isset($recogDistance) ? ($recogDistance == 4)? "selected":"" : ""?> value="4">3 meters</option>
            </select>
        </div>
        <div class="form-group">
            <label for="recogDistance">Recognition Interval</label>
            <input type="number" class="form-control" name="recogInterval" max="180" id="recogInterval" value="<?= isset($recogInterval) ? $recogInterval : "60"?>">
        </div>
        <div class="form-group">
            <label for="mask">Mask Detection</label>
            <select class="form-control chosen" id="mask" data-placeholder="Select">
                <option <?= isset($mask) ? ($mask == "Enable")? "selected":"" : ""?> value="Enable">Enable</option>
                <option <?= isset($mask) ? ($mask == "Disable")? "selected":"" : ""?> value="Disable">Disable</option>
            </select>
        </div>
        <div class="form-group">
            <label for="mask_dialogue">Mask Dialogue</label>
            <input type="text" class="form-control" name="mask_dialogue" id="mask_dialogue" value="<?= isset($mask_dialogue) ? $mask_dialogue : "Please wear your mask"?>">
        </div>
        <div class="form-group">
            <label for="file">Logo</label>
            <input type="file" id="file">
        </div>
        <div class="form-group">
            <label for="video_link">Screen Saver</label>
            <input type="text" id="video_link" class="form-control" value="<?= isset($video_link) ? $video_link : ""?>">
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

$(".chosen").chosen();

$("#button_save_modal").unbind('click').bind('click', function(event) {
    var serial_number = $("#serial_number").val();
    var ip = $("#ip").val();
    var name = $("#name").val();
    var recog = $("#recog").val();
    var campusid = $("#campusid").val();
    var file = $('#file')[0].files[0];
    var video_link = $('#video_link').val();
    var distance   =   $("#recogDistance").val();
    var recogScore   =   $("#recogScore").val();
    var recogInterval   =   $("#recogInterval").val();
    var mask   =   $("#mask").val();
    var mask_dialogue   =   $("#mask_dialogue").val();

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
    fd.append('mask', mask);
    fd.append('mask_dialogue', mask_dialogue);
    fd.append('video_link', video_link);


    if(serial_number != "" && name != "" && recog != ""){
        $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/saveFacialSetupFR')?>",
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