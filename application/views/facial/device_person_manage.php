<?php

/**
 * @author Kennedy Hipolito
 * @2019
 * @Updated UI
 */
$faceClass1 = $faceClass2 = $faceClass3 = "pendingClass";

if ($facial_status1 == "Success") {
    $faceClass1 = "successClass";
}elseif($facial_status1 == "Error"){
    $faceClass1 = "errorClass";
}else{
    $faceClass1 = "pendingClass";
}
if ($facial_status2 == "Success") {
    $faceClass2 = "successClass";
}elseif($facial_status2 == "Error"){
    $faceClass2 = "errorClass";
}else{
    $faceClass2 = "pendingClass";
}
if ($facial_status3 == "Success") {
    $faceClass3 = "successClass";
}elseif($facial_status3 == "Error"){
    $faceClass3 = "errorClass";
}else{
    $faceClass3 = "pendingClass";
}
?>
<style type="text/css">
    .pendingClass{
        color: blue;
    }
    .errorClass{
        color: red;
    }
    .successClass{
        color: green;
    }
</style>
<form>
    <div class="col-md-12">
        <div class="form-group" >
            <label for="serial_number">Device I.D</label>
            <input class="form-control" id="serial_number" name="serial_number" type="text" value="<?= isset($code) ? $code : ""?>" disabled/>
        </div>
        <div class="form-group">
            <label for="serial_number">Employee</label>
            <select class="chosen" id="employeeid">
            </select>
        </div>
        <div class="form-group">
            <label for="serial_number">Name</label>
            <input class="form-control" id="name" name="name" type="text" value="<?= isset($name) ? $name : ""?>"/>
        </div>
        <div class="row text-center">
            <div class="col-md-4">
                <div class="form-group">
                    <label for="file">Facial Feat. #1 <span class="<?= $faceClass1 ?>"><?= $facial_status1 ?></span></label>
                    <input type="file" id="file1" style='<?= ($facial_status1 == "Success")? "display: none;":"" ?>'><br>
                    <button type="button" class="viewPhoto btn btn-info center" style='<?= ($FaceId1 == "")? "display: none;":"" ?>' faceid="<?= $FaceId1 ?>">View</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group" >
                    <label for="file">Facial Feat. #2 <span class="<?= $faceClass2 ?>"><?= $facial_status2 ?></span></label>
                    <input type="file" id="file2" style='<?= ($facial_status2 == "Success")? "display: none;":"" ?>'><br>
                    <button type="button" class="viewPhoto btn btn-info" style='<?= ($FaceId2 == "")? "display: none;":"" ?>' faceid="<?= $FaceId2 ?>">View</button>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="file">Facial Feat. #3 <span class="<?= $faceClass3 ?>"><?= $facial_status3 ?></span></label>
                    <input type="file" id="file3" style='<?= ($facial_status3 == "Success")? "display: none;":"" ?>'><br>
                    <button type="button" class="viewPhoto btn btn-info" style='<?= ($FaceId3 == "")? "display: none;":"" ?>' faceid="<?= $FaceId3 ?>">View</button>
                </div>
            </div>
        </div>
    </div>
</form>
<script>
var action = "<?= $action ?>";
var ip = "<?= $ip ?>";
loadEmp("<?= $employeeId ?>");

$("#employeeid").change(function() {
    $("#name").val($(this).find('option:selected').text());
});

$("#button_save_modal").unbind('click').bind('click', function(event) {
    var serial_number = $("#serial_number").val();
    var employeeid = $("#employeeid").val();
    var name = $("#name").val();
    var card = $("#card").val();
    var file1 = $('#file1')[0].files[0];
    var file2 = $('#file2')[0].files[0];
    var file3 = $('#file3')[0].files[0];
    var fd = new FormData();
    if (file1 != "") {
        fd.append('file1', file1);
    }
    if (file2 != "") {
        fd.append('file2', file2);
    }
    if (file3 != "") {
        fd.append('file3', file3);
    }
    fd.append('name', name);
    fd.append('card', card);
    fd.append('empid', employeeid);
    fd.append('serial_number', serial_number);
    fd.append('action', action);
    Swal.fire({
        title: 'Please Wait !',
        html: 'Uploading facial feature to database.',// add html attribute if you want or remove
        allowOutsideClick: false,
        onBeforeOpen: () => {
            Swal.showLoading()
        },
    });

    if(serial_number != "" && name != "" && employeeid != ""){
        $.ajax({
        type: "POST",
        url: "<?= site_url('facial_/savePerson')?>",
        data: fd,
        dataType:"JSON",
        processData: false,  // tell jQuery not to process the data
        contentType: false,  // tell jQuery not to set contentType
        success:function(response){
            if (response.err_code == 1) {
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Data has been '+action+' successfully.',
                    showConfirmButton: true,
                    timer: 1500
                })
                $('#modal-view').modal('toggle');
                setTimeout(function() {
                    loadFacialSetupPerson();
                }, 1000);
            }else if(response.err_code == 2){
                Swal.fire({
                    icon: 'error',
                    title: 'Oops... Existing',
                    text: 'User is already registered.',
                    timer: 1500
                })
            }
            else{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Error Please Coordinate With Developer!',
                    timer: 1500
                })
            }
            
        }
        });
    }else{
        Swal.fire({
            icon: 'error',
            title: 'Oops...',
            text: 'All fields are required!',
            timer: 1500
        })
        loadFacialSetupPerson();
    }
});

function loadEmp(emp){
    $.ajax({
        url: "<?= site_url('facial_/loadEmployee') ?>",
        type: "POST",
        data: {
            emp: emp
        },
        success:function(response){
            $("#employeeid").html(response).trigger("chosen:updated");
        }
    });
}

$(".chosen").chosen();

$(".viewPhoto").click(function(){
    var mime = "";
    var data = "";
    var faceid = $(this).attr("faceid");
    $.ajax({
      url:"<?=site_url('facial_/getPersonImage')?>",
      type: "POST",
      data:{faceid:faceid,deviceKey:$("#serial_number").val()},
      dataType: "json",
      cache:true,
      async:true,
      success:function(response){
        var objectURL = URL.createObjectURL(b64toBlob(response.file, response.mime)) + '#toolbar=0&navpanes=0&scrollbar=0';
        window.open(objectURL, '_blank');
      }
    })
});

function b64toBlob(b64Data, contentType) {
    var byteCharacters = atob(b64Data)
    var byteArrays = []
    for (let offset = 0; offset < byteCharacters.length; offset += 512) {
        var slice = byteCharacters.slice(offset, offset + 512),
            byteNumbers = new Array(slice.length)
        for (let i = 0; i < slice.length; i++) {
            byteNumbers[i] = slice.charCodeAt(i)
        }
        var byteArray = new Uint8Array(byteNumbers)

        byteArrays.push(byteArray)
    }

    var blob = new Blob(byteArrays, { type: contentType })
    return blob
}
</script>