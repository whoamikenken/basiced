var refresh_recompute = true;
var toks = hex_sha512(" ");
$("input[name='checkall']").on("click", function(){
    if($("input[name='checkall']").prop("checked")){
        $('input[name="econfirm"]').each(function(){
            this.checked = true; 
        });
    }else{
        $('input[name="econfirm"]').each(function(){
            this.checked = false;
        });
    } 
});


function savingConfirmation(onload=false){
    var emplist = {};
    var count = 0;
    $('#asctblnt tr').filter(':has(:checkbox:checked)').find('td').each(function() {
        $(this).addClass("isconfirm");
        var idkey = $(this).find($("input[name='econfirm']")).val();
        if(idkey){
            emplist[idkey] = {
                toks:toks,
                empid : GibberishAES.enc(idkey, toks),
                dfrom : GibberishAES.enc($("#from_date").val(), toks),
                dto : GibberishAES.enc($("#to_date").val(), toks),
                tnt : GibberishAES.enc($("#tnt").val(), toks)
            };
            count++;
        }
    });
    $.ajax({ 
        url      : $("#site_url").val() + "/attendance_/saveConfirmation",
        type     : "POST",
        data     : emplist,
        success  : function(msg){
            if(msg == "success"){
                iniProcessConfirmation(true, count);
            }else if(msg == "ongoing"){
                iniProcessConfirmation(true, count);
            }else{
                if(!onload){
                    Swal.fire({
                        icon: 'warning',
                        title: 'Warning!',
                        text: 'Please select employee.',
                        showConfirmButton: true,
                        timer: 1000
                    });
                }
            }
        }
    });
}

function iniProcessConfirmation(firstload=false, count=''){
    if(firstload){
        Swal.fire({
            html: "<h4 id='processingMsg'>Processing Employees [0/"+count+"]</h4>",
            allowOutsideClick: false,
            allowEscapeKey: false,
            onRender: function() {
                $('.swal2-content').prepend(sweet_loader);
            }
        });
        $('.swal2-confirm').css("display", "none");
    }
    processingConfirmation();
}

function processingConfirmation(){
    $.ajax({ 
        url      : $("#site_url").val() + "/attendance_/processConfirmation",
        type     : "POST",
        data     : {toks:toks, dfrom : GibberishAES.enc($("#from_date").val(), toks), dto : GibberishAES.enc($("#to_date").val(), toks), tnt : GibberishAES.enc($("#tnt").val(), toks)},
        success  : function(msg){
            if(msg == 0){
                Swal.fire({
                    icon: 'success',
                    title: 'Success!',
                    text: "Employees data has been succesfully processed!",
                    showConfirmButton: true,
                    timer: 1000
                })
                $('.swal2-confirm').css("display", "unset");
                $("#enyc").click();
            }else{
                $("#processingMsg").html(msg);
                iniProcessConfirmation();
            }
        }
    });
}

$("#confirmattbtn").on("click", function(){
    savingConfirmation();
});

$("#confirmattbtns").on("click", function(){
    var loading = $("#loading").html();
    $("#cmsg").show().html(loading);

    var iscontinue = checkIfSystemIsRecomputing();
    if(!iscontinue){
        alert('Module is still recomputing attendance. Please wait.');
        $("#loading").show();
        loadRecomputePercentage(tnt); 
        setTimeout(function(){
            $("#cmsg").fadeOut().fadeOut("slow").fadeOut(2000);
        }, 3000);
        return;
    }

    loadRecomputePercentage(tnt); 
    var timer = setInterval(function(){
        loadRecomputePercentage(tnt); 
        if(!refresh_recompute){
            clearInterval(timer);
        }
    }, 2000);

    var emplist = {};
    $('#asctblnt tr').filter(':has(:checkbox:checked)').find('td').each(function() {
        $(this).addClass("isconfirm");
        var idkey = $(this).find($("input[name='econfirm']")).val();
        if(idkey){
            emplist[idkey] = {
                toks:toks,
                empid : GibberishAES.enc(idkey, toks),
                dfrom : GibberishAES.enc($("#from_date").val(), toks),
                dto : GibberishAES.enc($("#to_date").val(), toks),
                tnt : GibberishAES.enc($("#tnt").val(), toks)
            };
        }
    });
    $.ajax({ 
        url      : $("#site_url").val() + "/attendance_/validateConfirmAttendance",
        type     : "POST",
        data     : emplist,
        dataType : "json",
        success  : function(msg){
            refresh_recompute = false;
            Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: 'Processing data completed!',
                  showConfirmButton: true,
                  timer: 1000
              })
            $("#confirmattbtn").hide();
            $("#cmsg").text(msg.msg);
            $(".isconfirm").remove();
        }
    });
});

function checkIfSystemIsRecomputing(){
    var iscontinue = false;
    var tnt = $("#tnt").val();
    $.ajax({
        url : $("#site_url").val() + "/extensions_/checkIfSystemIsRecomputing",
        type : "POST",
        async: false,
        data : {tnt:tnt},
        success:function(response){
            iscontinue = response;
        }
    });

    return iscontinue;
}

function loadRecomputePercentage(tnt){
    $.ajax({
        url: $("#site_url").val() + '/process_/recomputePercentage',
        type: "POST",
        data: {tnt : GibberishAES.enc($("#tnt").val(), toks), toks:toks},
        success:function(response){
            $('#recompute_percentage').css("color", "green").html(response);
        }
    });
}