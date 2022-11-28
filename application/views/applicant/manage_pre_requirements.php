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
            <div class="form_row" style="display: none;" >
                <label class="field_name align_right">Code</label>
                <div class="field">
                    <input class="form-control" id="code" name="code" type="text" value="<?= isset($code) ? $code : ""?>" <?= ($tag == "edit") ? "disabled" : "" ?>/><span class="code_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
            <br>
            <div class="form_row">
                <label class="field_name align_right">Description</label>
                <div class="field">
                    <input class="form-control" id="description" name="description" type="text" style="width: 70%;" value="<?= isset($description) ? $description : ""?>"/><span class="description_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
            <br>
        </div>
        <div id="alert_message" style="height: 30px;width: 100%;margin:auto;font-size:15px;font-style:cursive;display: none;"></div><br>
        <div class="modal-footer">

            <button type="button" class="btn btn-danger" data-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="save">Save</button>
        </div>
    </div>
</div>

<script>
var toks = hex_sha512(" ");
$("#save").click(function(){
    var action = $("#tag").attr('action');
    var type = "pre";
    var code = $("#code").val();
    var description = $("#description").val();
    var employeeid = $("#employeeid").val();
    // if(code == ""){
    //     $("#code").css("border", "1px solid red");
    //     $('.code_mark').show();
    // }
    if(description == ""){
        $("#description").css("border", "1px solid red");
        $('.description_mark').show();
    }
    if(employeeid == ""){
        $('.employeeid_mark').show();
    }
    if(description != "" && employeeid != ""){
        $.ajax({
        type: "POST",
        url: "<?= site_url('setup_/saveRequiredRequirements')?>",
        data: {
            toks:toks,
            type:GibberishAES.enc(type, toks),
            code:GibberishAES.enc(code, toks),
            description:GibberishAES.enc(description, toks),
            employeeid:GibberishAES.enc(employeeid, toks),
            action:GibberishAES.enc(action, toks)
        },
        success:function(response){
            var notif = "";
            if(response == "add") notif = "saved";
            else if(response == "edit") notif = "updated";

            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Requirements has been '+notif+' successfully.',
                showConfirmButton: true,
                timer: 1000
            })
            
            preRequirements_setup();
            $('#myModal').modal('toggle');
        }
        });
    }else{
        alert("All fields are required. ");
    }
});

$(".chosen").chosen();
</script>