<?php
/**
* @author Max Consul
* @copyright 2019
*/
?>

<style>
	.cbox{
		-ms-transform: scale(1.5); /* IE */
		-moz-transform: scale(1.5); /* FF */
		-webkit-transform: scale(1.5); /* Safari and Chrome */
		-o-transform: scale(1.5); /* Opera */
	}
</style>


<div class="container" style="width: 100%;">
        <!-- <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
            <input class="form-control" id="code" type="text" value="<?= isset($code) ? $code : ""?>" <?= ($tag == "edit") ? "disabled" : "" ?> placeholder="Code"  required="required">
        </div><br> -->
        <br>
        <div class="col-md-12">
            <div class="col-md-3 fieldName">
                <label class="align_right">Description</label>
            </div>
            <div class="col-md-9">
                <input class="form form-control" id="description" type="text" value="<?= isset($description) ? $description : ""?>" placeholder="Description" required="required">
            </div>
        </div>
</div>
<input type="hidden" id="tag" action="<?= $tag ?>">
<input type="hidden" id="tableCode" action="<?= $tableCode ?>">
<input type="hidden" id="code" value="<?= isset($code) ? $code : "" ?>">
<script>
    $("#button_save_modal").unbind().click(function(){
        var action = $("#tag").attr('action');
        var tableCode = $("#tableCode").val();
        var code = $("#code").val();
        var description = $("#description").val();
        if(description == ""){
            $("#description").css("border", "1px solid red");
        }
        if(description != ""){
            $.ajax({
                type: "POST",
                url: "<?= site_url('setup_/saveHRSetup')?>",
                data: {code:code,description:description,action:action,tableCode:tableCode},
                success:function(response){
                    if(response == "add") alert('Successfully Saved');
                    else if(response == "edit") alert('Successfully Updated');
                    else alert('Entry Failed');
                
                    civilStatusSetup();
                    $("#modalclose").click();
                }
            });
        }
    });

    $('.chosen').chosen();
</script>