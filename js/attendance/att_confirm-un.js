$(document).ready(function(){
    if($("#showFinalize").val()){
        $(".finalize_div").show();
    }
});

$("#finalize").click(function(){
    $('.pdata').each(function() {
        var eid = $(this).find(".pdataid").text();
        $.ajax({ 
            url      : $("#site_url").val() + "/employeemod_/loadmodelfunc",
            type     : "POST",
            data     : {
                            model: "payrollconfirm",
                            tnt  : "",
                            dfrom: "",
                            dto  : "",
                            eid  : eid
                        },
            success  : function(msg){
                var data = $.parseJSON(msg);
                if(data[0])  $(".pdata,.pdept").remove();    
                $("#finalize").hide();
                $("#cmsg").text(data[1]);
                
            }
        });
    });
});

$("#ewc").click(function(){
   var loading = $("#loading").html();
   $("#cutofflist").show().html(loading);
   $.ajax({
       url      : $("#site_url").val() + "/setup_/getAttendanceConfirmedList",
       type     : "POST",
       data     : {
                    cdate   : $("#cutoff").val(),
                    deptid   : $("#deptid").val(),
                    office   : $("#office").val(),
                    campus   : $("#campus").val(),
                    empstatus   : $("#empstatus").val(),
                    empstat   : $("#empstat").val(),
                    employeeid   : $("select[name='employeeid']").val(),
                    tnt     : $("#tnt").val()
                  },
       success  :   function(msg){
        $("#cutofflist").html(msg);
        $("#myModalatt").modal('hide');       
       }
    });
});
$("#enyc").click(function(){
   var loading = $("#loading").html();
   $("#cutofflist").show().html(loading);
   $.ajax({
       url      : $("#site_url").val() + "/setup_/getAttendanceNotConfirmedList",
       type     : "POST",
       data     : {
                    cdate   : $("#cutoff").val(),
                    deptid   : $("#deptid").val(),
                    office   : $("#office").val(),
                    campus   : $("#campus").val(),
                    empstatus   : $("#empstatus").val(),
                    empstat   : $("#empstat").val(),
                    employeeid   : $("select[name='employeeid']").val(),
                    tnt     : $("#tnt").val()
                  },
       success  :   function(msg){
        $("#cutofflist").html(msg);
        $("#myModalatt").modal('hide');       
       }
    });
});