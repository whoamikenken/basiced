<link href="<?=base_url();?>css/terminal_setup/terminal.css" rel="stylesheet">
<div id="content">
    <div class="widgets_area">
        <div  style="margin-bottom: 20px;">
            <a class="btn btn-primary addbtn" id="addBtn" href="#modal-view" data-toggle="modal" ><i class="glyphicon glyphicon-plus-sign"></i><span class="" style="font-family: Tahoma;"> Add New </span></a>&emsp;<a class="btn btn-primary" id="logingate"><i class="glyphicon glyphicon glyphicon-log-in"></i><span class="" style="font-family: Tahoma;"> Log-in Gate </span></a>
        </div>
        
        <div id="inhouseSeminar">
            
        </div>
    </div>
</div>

<script>
var toks = hex_sha512(" ");
loadSeminar();

function loadSeminar(){
    $.ajax({
        url: "<?=site_url('employeemod_/loadSeminar')?>",
        success:function(res){
            $("#inhouseSeminar").html(res);
        }
    })
}

$('#addBtn').on('click',function(){
    addInhouseSeminar('');
})

$('#logingate').on('click',function(){
    var ams_url = window.location.href;
    ams_url  = ams_url .substring(0, ams_url .length - 9);
    window.open(ams_url + 'fingerprint_/seminar_gate');
})


function addInhouseSeminar(obj, tbl_id=''){
    $.ajax({
        url: "<?=site_url('employeemod_/manageInhouseSeminar')?>",
        type: 'POST',
        data: {tbl_id: GibberishAES.enc(tbl_id , toks), toks:toks},
        success:function(response){
            $("#modal-view").modal();
            $("#modal-view").find("#button_save_modal").removeAttr("tbl_id");
            if(tbl_id){
                $("#modal-view").find("h3[tag='title']").text("Edit Inhouse Seminar");
                $("#modal-view").find("#button_save_modal").removeClass();
                $("#modal-view").find("#button_save_modal").addClass("btn btn-success edit_btn");
                $("#modal-view").find("#button_save_modal").attr("tbl_id", tbl_id);
                $("#modal-view").find("#button_save_modal").text("Save");
            }else{
                $("#modal-view").find("h3[tag='title']").text("Add Inhouse Seminar");
                $("#modal-view").find("#button_save_modal").removeClass();
                $("#modal-view").find("#button_save_modal").addClass("btn btn-success add_btn");
                $("#modal-view").find("#button_save_modal").text("Save");
            }
            $("#modal-view").find("div[tag='display']").html(response);
        }
    });
}

$("#button_save_modal").on("click", function(){
    var iscontinue = true;
    var tbl_id = $(this).attr('tbl_id');
    var formdata   =  '';
    $('#inhouse_form input, #inhouse_form select, #inhouse_form textarea').each(function(){
      if(formdata) formdata += '&'+$(this).attr('name')+'='+$(this).val();
      else formdata = $(this).attr('name')+'='+$(this).val();
   })
    if(tbl_id){
        formdata += '&id='+tbl_id;
    }
    $("#inhouse_form input, select").each(function(){
        if(!$(this).val() && $(this).attr("name") != "id" && $(this).attr("name") != "attendeesDept[]" && $(this).attr("name") != "attendeesOffice[]" && $(this).attr("name") != "employees[]" && !typeof $(this).attr("name") === "undefined"){
            $(this).css("border", "1px solid red");
            iscontinue = false;
        }else{
            $(this).css("border", "1px solid #ccc");
        }
    });

    if(!iscontinue) return;

    var issameid = checkIfExisting(tbl_id);
    if(issameid > 0){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Username already exists. Please try another username",
            showConfirmButton: true,
            timer: 1000
        })
        $("input[name='username']").css("border-color", "1px solid red");
        return;
    }else{
        $("input[name='username']").css("border-color", "1px solid #ccc");
    }

    $.ajax({
        url: $("#site_url").val() + "/seminar_/validateInhouseSeminar",
        type: "POST",
        data: {formdata:GibberishAES.enc(formdata, toks), toks:toks},
        dataType: "json",
        success:function(response){
            if(response.stat){
                if(response.msg == 'Successfully save inhouse seminar.'){
                    if($response.update == 0){
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Inhouse Seminar has been saved successfully.',
                            showConfirmButton: true,
                            timer: 1000
                        })
                    }else{
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Inhouse Seminar has been updated successfully.',
                            showConfirmButton: true,
                            timer: 1000
                        })
                    }
                        
                }else{
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: response.msg,
                        showConfirmButton: true,
                        timer: 1000
                    })
                }
                $("#modalclose").click();
                loadSeminar();
                // $("#modal-view").modal("toggle");
            }else{
                $("#msg_header").removeClass("alert alert-success");
                $("#msg_header").addClass("alert alert-danger");
                $("#msg_header").find("strong").text("Failed! ");
                $("#msg_header").find("span").text(response.msg);
                $("#msg_header").fadeIn().fadeIn("slow").fadeOut(5000);
            }
        }
    });
});

function checkIfExisting(tbl_id){
    var ret_obj = '';
    $.ajax({
        url: "<?=site_url('seminar_/checkIfExisting')?>",
        type: "POST",
        data:{username: GibberishAES.enc( $("input[name='username']").val(), toks), tbl_id: GibberishAES.enc( tbl_id, toks), toks:toks},
        async: false,
        success:function(response){
            ret_obj = response;
        }
    });
    return ret_obj;
}

</script>