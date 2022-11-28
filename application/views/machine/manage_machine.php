    <!-- Modal content-->
    <div class="row" style="width: 100%;">
            <form id = "machine_form">
                  <div class="form_row">
                    <label class="field_name align_right">Username</label>
                    <div class="field">
                        <input type="hidden" name="id" value="<?= isset($id) ? $id : '' ?>">
                        <input type="text" name="username" id="username" class="span4 form-control isrequired" value="<?= isset($username) ? $username : '' ?>"/>
                        <span style="color: red;display: none;" id="warning">&nbsp;&nbsp;Username ID already exist!</span>
                        <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Terminal</label>
                    <div class="field" style="margin-top: 8px";>
<!--                         <input type="hidden" name="id" value="<= isset($id) ? $id : '' ?>"> -->
                        <input type="text" name="terminal_name" class="span4 form-control isrequired" value="<?= isset($terminal_name) ? $terminal_name : '' ?>"/>
                        <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Campus</label>
                    <div class="field" style="margin-top: 8px";>
                        <select class="form-control isrequired chosen" name="campus" id="campus">
                        <!-- <option value="">No Selected</option> -->
                        <?php foreach($campus_list as $key => $campus_row):?>
                            <option <?= isset($campus) ? $key == $campus ? 'selected' : '' : '' ?> value="<?= $key ?>"><?= $campus_row ?></option>
                        <?php endforeach;?>
                    </select>
                    <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Building</label>
                    <div class="field" style="margin-top: 8px";>
                        <select class="form-control  chosen" name="building" id="building">
                        <option value="">No Selected</option>
                        <?php foreach($building_list as $key => $building_row):?>
                            <option <?= isset($building) ? $key == $building ? 'selected' : '' : '' ?> value="<?= $key ?>"><?= $building_row ?></option>
                        <?php endforeach;?>
                    </select>
                    <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right">Floor</label>
                    <div class="field" style="margin-top: 8px";>
                        <select class="form-control  chosen" name="floor" id="floor">
                        <option value="">No Selected</option>
                        <?php foreach($floor_list as $key => $floor_row):?>
                            <option <?= isset($floor) ? $key == $floor ? 'selected' : '' : '' ?>  value="<?= $key ?>"><?= $floor_row ?></option>
                        <?php endforeach;?>
                    </select>
                    <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                    </div>
                </div>
                <!-- <div class="form_row">
                    <label class="field_name align_right">Template</label>
                    <div class="field" style="margin-top: 8px";>
                        <select class="form-control  chosen" name="template" id="template">
                            <option value="1" <?= ($template == 1)? "selected":"" ?>>Template 1</option>
                            <option value="2" <?= ($template == 2)? "selected":"" ?>>Template 2</option>
                        </select>
                    <span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                    </div>
                </div> -->
              <div class="form_row">
                    <label class="field_name align_right">Password</label>
                    <div class="field" style="margin-top: 8px";>
                       <div><input name="password" id="password" type="password" class="span4 form-control" value="" /></div>
                   </div>
                </div>
                  <div class="form_row">
                    <label class="field_name align_right">Re-Type Password</label>
                    <div class="field" style="margin-top: 8px";>
                   <div><input type="password" name="rt_password" id="rt_password" class="span4 form-control" value=""/>
                     <span class="messagess" id='messagess'><b></b></span></div>
                 </div>
                </div>
            </form>
   </div>

<!-- <script src="<=base_url()?>js/terminal_setup/terminal_manage.js"></script> -->

<script type="text/javascript">
var toks = hex_sha512(" ");
var iscontinue = true;
$("#rt_password, #password").keypress(function(){
    $("#messagess").text("");
});
$("#rt_password, #password").on('blur, change, keyup', function(){
   var npass = $("#rt_password").val();
   var pass = $("#password").val();
   if(npass != "" && pass != ""){
       if(npass != pass)   $("#messagess").css({"color":"red"}).text("Password did not match!");
       else                $("#messagess").css({"color":"green"}).text("Password Match!");
   }
});

$(".add_btn").unbind().click(function(){
    if($("#password").val() != '' ||  $("#rt_password").val() != ''){
            if($("#password").val() != $("#rt_password").val()) return;

    }
    
    if(!iscontinue){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Username already exists!',
            showConfirmButton: true,
            timer: 1000
        })
        return;
    }
    var iscontinues = validateForm($("#machine_form"));
    if(iscontinues){
        var formdata = $("#machine_form").serialize();
        $.ajax({
            url: $("#site_url").val() + "/machine_/validateGateAccount",
            type: "POST",
            data: {formdata:GibberishAES.enc(formdata, toks), toks:toks},
            success:function(){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Terminal has been saved successfully',
                    showConfirmButton: true,
                    timer: 1000
                })
                $("#dtr-modal").find("#save-dtr-setup").removeClass();
                $("#dtr-modal").modal('toggle');
                $(".modalclose").click();
                loadTerminalList();
            }
        });
    }
});

$(".edit_btn").unbind().click(function(){

    if($("#password").val() != '' ||  $("#rt_password").val() != ''){
            if($("#password").val() != $("#rt_password").val()) return;

    }
    if(!iscontinue){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Username already exists!',
            showConfirmButton: true,
            timer: 1000
        })
        return;
    }
    var formdata = $("#machine_form").serialize();
    $.ajax({
        url: $("#site_url").val() + "/machine_/validateGateAccount",
        type: "POST",
        data: formdata,
        success:function(){
            Swal.fire({
                icon: 'success',
                title: 'Success!',
                text: 'Terminal has been updated successfully',
                showConfirmButton: true,
                timer: 1000
            })
            $("#modal-view").find("#save-dtr-setup").removeClass();
            $("#modal-view").modal('toggle');
            $(".modalclose").click();
            loadTerminalList();
        }
    });
});

$("input[name='username']").on("change", function(){
    $.ajax({
        url: $("#site_url").val() + "/machine_/isUsernameExist",
        type: "POST",
        data: { username : $(this).val() },
        success:function(response){
            if(response>=1){
                iscontinue = false;
                $("input[name='username']").css("border-color", "red");
                $("#warning").show();
            }
            else{
                iscontinue = true;
                $("#warning").hide();
                $("input[name='username']").css("border-color", "black");
            }
        }
    });
});

$(".chosen").chosen();

</script>