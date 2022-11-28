<?php

/**
 * @author Kennedy Hipolito
 * @2019
 * @Updated UI
 */

?>
<div class="modal-dialog">

    <!-- Modal content-->
    <div class="modal-content">
        <div class="modal-header">
            <div class="modal-header">
                <div class="media">
                    <div class="media-left">
                        <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
                    </div>
                    <div class="media-body" style="font-weight: bold;padding-top: 10px;">
                        <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
                    </div>
                </div>
            <center><b><h3 id="tag" action="<?=$tag?>" tag="title" class="modal-title"><?=$title?></h3></b></center>
        </div>
        <div class="modal-body">
            <br>
            <div class="form_row">
                <label class="field_name align_right">Serial Number</label>
                <div class="field">
                    <input class="form-control" id="serial_number" name="serial_number" type="text" value="<?= isset($serial_number) ? $serial_number : ""?>" <?= ($tag == "edit") ? "disabled" : "" ?>/><span class="code_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
            <br>
            <div class="form_row">
                <label class="field_name align_right">Name</label>
                <div class="field">
                    <input class="form-control" id="name" name="name" type="text" value="<?= isset($name) ? $name : ""?>"/><span class="description_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
            <br>
            <div class="form_row">
                <label class="field_name align_right">I.P Address</label>
                <div class="field">
                    <input class="form-control" id="ip" name="ip" type="text" value="<?= isset($ip) ? $ip : ""?>"/><span class="ip_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
        </div>
        <div id="alert_message" style="height: 30px;width: 100%;margin:auto;font-size:15px;font-style:cursive;display: none;"></div><br>
        <div class="modal-footer">
            <button type="button" class="btn btn-success" id="save">Save</button>
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
        </div>
    </div>
</div>

<script>

$("#save").click(function(){
    var action = $("#tag").attr('action');
    var serial_number = $("#serial_number").val();
    var name = $("#name").val();
    var ip = $("#ip").val();
    if(serial_number == ""){
        $("#serial_number").css("border", "1px solid red");
        $('.code_mark').show();
    }
    if(name == ""){
        $("#name").css("border", "1px solid red");
        $('.description_mark').show();
    }
    if(ip == ""){
        $('.ip_mark').show();
    }
    if(serial_number != "" && name != "" && ip != ""){
        $.ajax({
        type: "POST",
        url: "<?= site_url('setup_/saveFacial')?>",
        data: {serial_number:serial_number,name:name,ip:ip,action:action},
        success:function(response){
            if(response == "add"){
                alert('Successfully Saved');
            }else if(response == "edit"){
                alert('Successfully Updated');
            }
            else{
                $("#alert_message").fadeIn().fadeIn("slow").fadeIn(3000).fadeOut(3000);
                $("#alert_message").css({"background-color": "#d16f6a","color": "white"});
                alert('Entry Failed');
            }
            loadFacialSetup();
            $('#myModal').modal('toggle');
        }
        });
    }else{
        alert("All fields are required. ");
    }
});

$(".chosen").chosen();

</script>