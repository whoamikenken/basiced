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

<input type="hidden" id="tag" action="<?= $tag ?>">
<div class="container" style="width: 100%;">
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
            <input class="form-control" id="code" type="text" value="<?= isset($code) ? $code : ""?>" <?= ($tag == "edit") ? "disabled" : "" ?> placeholder="Code"  required="required">
        </div><br>
        <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-list-alt"></i></span>
            <input class="form-control" id="description" type="text" value="<?= isset($description) ? Globals::_e($description) : ""?>" placeholder="Description" required="required">
        </div>
        <!-- <div class="input-group">
            <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>
            <select class="chosen" id="head" name="head">
                <option value="<?= $depthead?>"><?= $this->extensions->getEmployeeName($depthead) ?></option>
                <?=$this->employee->loadallempid($depthead)?>
            </select>
        </div><br> -->
</div>

<script>
    var toks = hex_sha512(" ");
    $("#button_save_modal").click(function(){
        var action = $("#tag").attr('action');
        var code = $("#code").val();
        var description = $("#description").val();
        var head = $("#head").val();
        if(code == ""){
            $("#code").css("border", "1px solid red");
        }
        if(description == ""){
            $("#description").css("border", "1px solid red");
        }
        if(code != "" && description != ""){
            $.ajax({
                type: "POST",
                url: "<?= site_url('setup_/saveDepartment2')?>",
                data: {
                        toks:toks,
                        code:GibberishAES.enc(code, toks),
                        description:GibberishAES.enc(description, toks),
                        head:GibberishAES.enc(head, toks),
                        action:GibberishAES.enc(action, toks)
                    },
                success:function(response){
                    var notif = "";
                    if(response == "add") notif = "saved";
                    else if(response == "edit") notif = "updated";
                    if (response == "duplicate") {
                        Swal.fire({
                            icon: 'warning',
                            title: 'Warning!',
                            text: 'Department code is already existing.',
                            showConfirmButton: true,
                            timer: 1000
                        })
                        
                    }else{
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Department has been '+notif+' successfully.',
                            showConfirmButton: true,
                            timer: 1000
                        })
                        department_setup(); 
                        $('#modalclose').click();
                    }
                
                    } }); } });

    $('.chosen').chosen();
</script>