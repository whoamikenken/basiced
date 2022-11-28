<?php

    /**
    * @author Max Consul
    * @copyright 2019
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
                <center><b><h3  id="tag" action="<?=$tag?>" tag="title" class="modal-title"><?=$title?></h3></b></center>
        </div>
        <div class="modal-body">
            <br>
            <div class="form_row">
                <label class="field_name align_right">Code</label>
                <div class="field">
                    <input class="form-control" id="code" name="code" type="text" value="<?= isset($code) ? $code : ""?>" <?= ($tag == "edit") ? "disabled" : "" ?>/><span class="code_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
            <br>
            <div class="form_row">
                <label class="field_name align_right">Description</label>
                <div class="field">
                    <input class="form-control" id="description" name="description" type="text" value="<?= isset($description) ? $description : ""?>"/><span class="description_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
            <br>
            <div class="form_row">
                <label class="field_name align_right">Principal</label>
                <div class="field">
                    <select class="chosen form-control" id="employeeid" name="employeeid">
                        <option value=""> --- Select assigned Principal --- </option>
                        <?php foreach($emplist as $value): ?>
                            <option value="<?= $value['employeeid'] ?>" <?= ($value['employeeid'] == $principal) ? "selected" : "" ?> > <?= $value['fullname'] ?></option>
                        <?php endforeach ?>
                    </select><span class="employeeid_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
        </div>
        <div id="alert_message" style="height: 30px;width: 100%;margin:auto;font-size:15px;font-style:cursive;display: none;"></div><br>
        <div class="modal-footer">
            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
            <button type="button" class="btn btn-success" id="save">Save</button>
        </div>
    </div>

</div>

<script>
$("#save").click(function(){
    var action = $("#tag").attr('action');
    var code = $("#code").val();
    var description = $("#description").val();
    var employeeid = $("#employeeid").val();
    if(code == ""){
        $("#code").css("border", "1px solid red");
        $('.code_mark').show();
    }
    if(description == ""){
        $("#description").css("border", "1px solid red");
        $('.description_mark').show();
    }
    if(employeeid == ""){
        $('.employeeid_mark').show();
    }
    if(code != "" && description != "" && employeeid != ""){
        $.ajax({
        type: "POST",
        url: "<?= site_url('setup_/saveCampus')?>",
        data: {code:code,description:description,employeeid:employeeid,action:action},
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
            campus_setup();
            $('#myModal').modal('toggle');
        }
        });
    }else{
        alert("All fields are required. ");
    }
});

$(".chosen").chosen();
</script>