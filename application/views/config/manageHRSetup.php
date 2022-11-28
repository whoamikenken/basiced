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
    var toks = hex_sha512(" ");
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
                data: {
                    toks: toks,
                    code:GibberishAES.enc(code, toks),
                    description:GibberishAES.enc(description, toks),
                    action:GibberishAES.enc(action, toks),
                    tableCode:GibberishAES.enc(tableCode, toks)
                },
                success:function(response){
                    var notif = "";
                    var table = "";
                    if(response == "add") notif = "saved";
                    else if(response == "edit") notif = "updated";

                    if(tableCode == "code_civil_status"){
                        table = "Civil Status";
                        civilStatusSetup();
                    } 
                    else if(tableCode == "code_gender"){
                        table = "Gender";
                        genderSetup();
                    }
                    else if(tableCode == "code_nationality"){
                        table = "Nationality"; 
                        nationalitySetup(); 
                    } 
                    else if(tableCode == "code_religion"){
                        table = "Religion";
                        religionSetup();
                    } 
                    else if(tableCode == "code_citizenship"){
                        table = "Citizenship";
                        citizenshipSetup();
                    } 
                    else if(tableCode == "code_relationship"){
                        table = "Relationship";
                        relationshipSetup();
                    } 
                    else if(tableCode == "code_managementlevel"){
                        managementLevelSetup();
                    }
                    else if(tableCode == "code_school"){
                        table = "School";
                        schoolSetup();
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: 'Success!',
                        text: table+' has been '+notif+' successfully.',
                        showConfirmButton: true,
                        timer: 1000
                    })

                    $("#modalclose").click();
                }
            });
        }
    });

    $('.chosen').chosen();
</script>