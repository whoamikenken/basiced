<?php

    /**
    * @author Kennedy Hipolito
    * @copyright 2019
    */

?>
<input type="hidden" id="ip" name="ip" value="<?= $ip ?>">
<input type="hidden" id="tag" name="tag" value="<?= $tag ?>">
<input type="hidden" id="serial_number" name="serial_number" value="<?= $serial_number ?>">
<div class="form_row">
    <label class="field_name align_right">Person ID</label>
    <div class="field" style="width: 70%">
        <input class="form-control" id="id" name="id" type="text" value="<?= isset($id) ? $id : ""?>"/><span class="code_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
    </div>
</div>
<br>
<div class="form_row">
    <label class="field_name align_right">Employee</label>
    <div class="field" style="width: 70%">
        <select class="chosen" name="employeeid" id="employee">
            <option value="">All Employee</option>
            <?php foreach($emplist as $row): ?>
                <option value="<?= $row['employeeid'] ?>"><?= $row['fullname'] ?></option>   
            <?php endforeach ?>
        </select>
    </div>
</div>
<br>
<div class="form_row">
    <label class="field_name align_right">Name</label>
    <div class="field" style="width: 70%">
        <input class="form-control" id="name" name="name" type="text" value="<?= isset($name) ? $name : ""?>"/><span class="name_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
    </div>
</div>
<br>
<div class="form_row">
    <label class="field_name align_right">Card Number</label>
    <div class="field" style="width: 70%">
        <input class="form-control" id="card" name="idcardNum" type="text" value="<?= isset($idcardNum) ? $idcardNum : ""?>"/><span class="card_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
    </div>
</div>
<br>
<div class="form_row">
    <label class="field_name align_right">Picture</label>
    <div class="field">
       <input type="file" name="picture" id="picture"><span class="picture_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
    </div>
</div>
<br><br>
<div class="form_row" id="pict" style="display: none;"><center>
    <label class="field_name align_right"><p id="b64" style="display: none;"></p></label>
    <div class="field">
       <img id="img" height="450">
    </div>
    </center>
</div>
<script>
var ip = $("#ip").val();
var serial_number = $("#serial_number").val();

$("#button_save_modal").click(function(){
    var action = $("#tag").val();
    var id = $("#id").val();
    var name = $("#name").val();
    var card_number = $("#card").val();
    var image = $("#b64").text();
    var emp = $("#employee").val();

    if(id == ""){
        $("#id").css("border", "1px solid red");
        $('.code_mark').show();
        return;
    }
    if(name == ""){
        $("#name").css("border", "1px solid red");
        $('.name_mark').show();
        return;
    }
    if(card_number == ""){
        $("#card_number").css("border", "1px solid red");
        $('.card_mark').show();
        return;
    }
    if(image == ""){
        alert("Please Provide Face Image.");
        return;
    }
    if(image == ""){
        alert("Please Select Employee.");
        return;
    }
    if(id != "" && name != "" && card_number != "" && image != ""){
        var data = {pass:"12345678", person:'{"id":"'+ id +'","idcardNum":"'+ card_number +'","name":"'+ name +'"}'};
        $.ajax({
        type: "POST",
        url: 'http://'+ ip +':8090/person/create',
        data: data,
        success:function(response){
            response = JSON.parse(response);
            if (response.success) {
                var photoData = {pass:"12345678", personId:id, faceId:"", imgBase64:image, isEasyWay:true};
                $.ajax({
                type: "POST",
                url: 'http://'+ ip +':8090/face/create',
                data: photoData,
                success:function(res){
                    res = JSON.parse(res);
                    if (res.success){
                        $.ajax({
                                type: "POST",
                                url: "<?= site_url('setup_/savePersonToDevice')?>",
                                data: {serial_number:serial_number,personID:id,employeeid:emp,action:action,card_number:card_number},
                                success:function(response){
                                    if(response == "add"){
                                        alert('Successfully Saved');
                                    }else if(response == "edit"){
                                        alert('Successfully Updated');
                                    }else alert("Error!");
                                    $('#modal-view').modal('hide');
                                    facial_device(serial_number);
                                }
                            });
                    }else {
                        alert("Error!");
                        $('#modal-view').modal('hide');
                    } 
                }
                });
            }else {
                alert("Error!");
                $('#modal-view').modal('hide');
            }
        }
        });
    }else{
        alert("All fields are required.");
    }
});

$("#picture").change(function() { // bCheck is a input type button
    if (this.files && this.files[0]) {
    $("#pict").show();
    var FR = new FileReader();
    
    FR.addEventListener("load", function(e) {
      document.getElementById("img").src       = e.target.result;
      document.getElementById("b64").innerHTML = e.target.result.split(",")[1];
    }); 
    
    FR.readAsDataURL( this.files[0] );
  }
});


$(".chosen").chosen();
</script>