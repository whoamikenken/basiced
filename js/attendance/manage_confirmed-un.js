var toks = hex_sha512(" ");
$("input[name='checkall']").on("click", function(){
    if($("input[name='checkall']").prop("checked")){
        $('input[name="econfirm"]').each(function(){
            if($(this).attr("status") != "PROCESSED") this.checked = true; 
        });
    }else{
        $('input[name="econfirm"]').each(function(){
            this.checked = false;
        });
    } 
});
$("#confirmattbtn").on("click", function(){
    var iscontinue = false;
    var emplist = {};
    $('#asctblnt tr').filter(':has(:checkbox:checked)').find('td').each(function() {
        $(this).addClass("isconfirm");
        var idkey = $(this).find($("input[name='econfirm']")).val();
        if(idkey){
            iscontinue = true;
            emplist[idkey] = {
                toks: toks,
                empid : GibberishAES.enc(idkey, toks),
                dfrom : GibberishAES.enc($("#from_date").val(), toks),
                dto : GibberishAES.enc($("#to_date").val(), toks),
                tnt : GibberishAES.enc($("#tnt").val(), toks)
            };
        }
    });

    if (!iscontinue) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please select employee.',
            showConfirmButton: true,
            timer: 1000
        });
        return;
    }else{
        Swal.fire({
            html: "<h4 id='processingMsg'>Processing Employees <br> Please wait..</h4>",
            allowOutsideClick: false,
            allowEscapeKey: false,
            onRender: function() {
                $('.swal2-content').prepend(sweet_loader);
            }
        });
        $('.swal2-confirm').css("display", "none");
    }

    // var loading = $("#loading").html();
    // $("#cmsg").show().html(loading);
    $.ajax({ 
        url      : $("#site_url").val() + "/attendance_/validateUnconfirmAttendance",
        type     : "POST",
        data     : emplist,
        dataType : "json",
        success  : function(msg){
                Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: 'Processing data completed!',
                  showConfirmButton: true,
                  timer: 1000
              })
            $('.swal2-confirm').css("display", "unset");
            $("#ewc").click();
            $("#confirmattbtn").hide();
            $("#cmsg").text(msg.msg);
            $(".isconfirm").remove();
        }
    });
});