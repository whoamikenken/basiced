        <div class="row">
            <center><b><h3  id="tag" action="<?=$tag?>" tag="title" class="modal-title"><?= $title ?></h3></b></center>
            <br>
            <div class="form_row">
                <label class="field_name align_right">Code</label>
                <div class="field">
                    <select class="chzn-select col-md-6 code-list chosen-select" multiple name="code[]" id="code">
                        <option value=""> --- Select campus --- </option>
                        <?php foreach($campuslist as $value): ?>
                            <?php if(isset($id)): ?>
                                <?php foreach(explode(",", $code) as $cam_code): ?>
                                    <option value="<?= $value['code'] ?>" <?= ($cam_code == $value["code"]) ? "selected" : "" ?> > <?= $value['description'] ?></option>
                                <?php endforeach ?>
                            <?php endif ?>
                        <?php endforeach ?>
                    </select><span class="code_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
            <br>
            <div class="form_row">
                <label class="field_name align_right">Employee</label>
                <div class="field">
                    <select class="chzn-select col-md-6 employeeid-list chosen-select" multiple name="employeeid[]" id="employeeid">
                        <option value=""> --- Select assigned employee --- </option>
                        <?php foreach($emplist as $value): ?>
                             <?php if(isset($id)): ?>
                                <?php foreach(explode(",", $employee) as $emp_id): ?>
                                    <option value="<?= $value['employeeid'] ?>"  <?= ($emp_id == $value["employeeid"]) ? "selected" : "" ?> > <?= $value['fullname'] ?></option>
                                <?php endforeach ?>
                            <?php endif ?>
                        <?php endforeach ?>
                    </select><span class="employeeid_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                </div>
            </div>
        </div>
        <div id="alert_message" style="height: 30px;width: 100%;margin:auto;font-size:15px;font-style:cursive;display: none;"></div><br>
    </div>
</div>
<input type="hidden" id="bypassid" value="<?=isset($id) ? $id : ''?>">
<script>

    $(document).ready(function(){
        $(".chosen").chosen();
        $(".chzn-select").chosen();
        $(".default").css("width", "100%");
    });

    $("#employeeid").on("change", function(){
        var elementId = $(this).attr("id");
        var value = $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").show();});
    });

    $("#code").on("change", function(){
        var elementId = $(this).attr("id");
        var value = $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").show();});
    });

    $("#save-dtr-setup").click(function(){
        var action = $("#tag").attr('action');
        var code = $("#code").val();
        var employeeid = $("#employeeid").val();
        if(code == ""){
            $("#code").css("border", "1px solid red");
            $('.code_mark').show();
        }
        if(employeeid == ""){
            $('.employeeid_mark').show();
        }
        if(code != "" && employeeid != ""){
            $.ajax({
            type: "POST",
            url: "<?= site_url('setup_/saveBypass')?>",
            data: {code:code,employeeid:employeeid,bypassid:$("#bypassid").val(),action:action},
            success:function(response){
                location.reload();
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
                bypass_setup();
                $('#dtr-modal').modal('toggle');
            }
            });
        }else{
            alert("All fields are required. ");
        }
    });

</script>