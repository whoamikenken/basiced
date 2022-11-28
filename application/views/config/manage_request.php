<style>
.form_row label.field_name, .form_row span.field_name {
    margin-right: 30px;
}
</style>

        <div class="row">
            <center><b><h3  id="tag" action="<?=$tag?>" tag="title" class="modal-title"><?= $title ?></h3></b></center>
            <br>
            <form id="manageReqForm">
                <input type="hidden" name="id" id="id" value="<?= isset($id) ? $id : '' ?>">
                <div class="form_row" hidden="">
                    <label class="field_name align_right" style="padding-right: 2%">Code</label>
                    <div class="field">
                        <input class="form-control" id="request_code" name="request_code" type="text" value="<?= isset($request_code) ? $request_code : ""?>" style="width: 90%" /><span class="code_mark" style="color:red;display: none;">&nbsp;&nbsp;*</span>
                    </div>
                </div>
                <div class="form_row">
                    <label class="field_name align_right" style="padding-right: 2%">Description</label>
                    <div class="field">
                        <input class="form-control isrequired" id="description" name="description" type="text"value="<?= isset($description) ? $description : ""?>"  style="width: 90%"/><span class="req-mark" style="color:red;display: none;">&nbsp;&nbsp;* This field is required</span>
                    </div>
                </div>
            </form>
            <div class="msg_header" style="display: none;">
                <strong></strong><span></span>
            </div>
        </div>
        <div id="alert_message" style="height: 30px;width: 100%;margin:auto;font-size:15px;font-style:cursive;display: none; "></div><br>
<script>
var toks = hex_sha512(" "); 
$(".save-dtr-setup").click(function(){
    var form_data = {
        toks: toks,
        action: GibberishAES.enc($("#tag").attr('action'), toks),
        request_code: GibberishAES.enc($("#request_code").val(), toks),
        description: GibberishAES.enc($("#description").val(), toks),
        id: GibberishAES.enc($("#id").val(), toks)
    }
    var iscontinue = validateForm($("#manageReqForm"));
    if(iscontinue){
        $.ajax({
        type: "POST",
        url: "<?= site_url('setup_/saveRequest')?>",
        data: form_data,
        success:function(response){
                location.reload();
            if(response == "add"){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Remark has been saved successfully.',
                    showConfirmButton: true,
                    timer: 1000
                })
            }else if(response == "edit"){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Remark has been updated successfully.',
                    showConfirmButton: true,
                    timer: 1000
                })
            }
            else{
                $("#alert_message").fadeIn().fadeIn("slow").fadeIn(3000).fadeOut(3000);
                $("#alert_message").css({"background-color": "#d16f6a","color": "white"});
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: 'Successfully Updated!',
                    showConfirmButton: true,
                    timer: 1000
                })
            }
            request_setup();
            $('#dtr-modal').modal('toggle');
        }
        });
    }
});

$(".chosen").chosen();
</script>