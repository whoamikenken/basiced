// getEmployeeList("active");
var loading_img = $("#loading_img").html();
var iscontinue = false;
var toks = hex_sha512(" ");
$("#sync_api_emp").click(function(){
    var teachingType = GibberishAES.enc($('#teachingType').val(), toks);
    var office = GibberishAES.enc($('#office').val(),toks);
    var department = GibberishAES.enc($('#department').val(), toks);
    var status = GibberishAES.enc($('#status').val(), toks);
    var employeeid = GibberishAES.enc($('#employeeid').val(), toks);
    var empstat = GibberishAES.enc($('#empstat').val(), toks);

    $(this).hide();
    $("#loading_syncemp").show();
    $.ajax({
        url: $("#site_url").val() + "/setup_/syncEmployeeListAllcard",
        type: "POST",
        data:{
            toks:toks,
            teachingType:teachingType,
            office:office,
            department:department,
            status:status,
            employeeid:employeeid,
            empstat:empstat
        },
        success:function(response){
            // alert(response);
             Swal.fire({
                  icon: 'success',
                  title: 'Success!',
                  text: response,
                  showConfirmButton: true,
                  timer: 2000
              })
            setTimeout(function(){ location.reload(); }, 2000);
        }
    });
});

$("#sync_api_stud").click(function(){
    $(this).hide();
    $.ajax({
        url: $("#site_url").val() + "/setup_/syncStudentListAllcard",
        success:function(response){
            alert(response);
            $(this).show();
            $("#loading_syncemp").hide();
        }
    });
});

$("#addnewemployee").click(function(){
    $("input[name='fname']").val("");
    $("input[name='lname']").val("");
    $("input[name='mname']").val("");
    $("input[name='employeeid']").val("");
    $("#add_employee").modal('toggle');
});

$("#save_emp").on("click", function()
{
    var toAims = "0";
    if($("input[name='aimcheckbox']").is(":checked")) toAims = "1";
    if ($('#teaching').prop('checked') == true) var teachingtype = 'teaching';
    if ($('#nonteaching').prop('checked') == true) var teachingtype = 'nonteaching';
    if(!iscontinue)
    {
        $("#loading").show();
        $.ajax(
        {
            url: $("#site_url").val() + "/employee_/saveNewEmployee",
            type: "POST",
            data: {
                fname:GibberishAES.enc($("input[name='fname']").val(), toks), 
                lname:GibberishAES.enc($("input[name='lname']").val(), toks), 
                mname:GibberishAES.enc($("input[name='mname']").val(), toks), 
                employeeid:GibberishAES.enc($("input[name='employeeid']").val(), toks), 
                teachingtype:GibberishAES.enc(teachingtype, toks), 
                campus:GibberishAES.enc($("select[name='campus_code']").val(), toks), 
                aimcheckbox:GibberishAES.enc(toAims, toks), 
                toks:toks
            },
            dataType: "json",
            success:function(response)
            {
                // getEmployeeList("active");
                loadEmplistTableSort();
                if(response.status == 1)
                {
                     Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: response.msg,
                          showConfirmButton: true,
                          timer: 2000
                    })
                    $("#loading").hide();
                    $("#add_employee").modal('toggle');
                }else if(response.status == 3){
                    Swal.fire({
                          icon: 'error',
                          title: 'ERROR!',
                          text: response.msg,
                          showConfirmButton: true,
                          timer: 5000
                    })
                }else{
                    Swal.fire({
                          icon: 'warning',
                          title: 'Warning!',
                          text: response.msg,
                          showConfirmButton: true,
                          timer: 2000
                      })
                }
                setTimeout(function(){ location.reload(); }, 2000);
            }
        });
    }
    else
    {
        // alert('Employee ID is exisiting!');
        Swal.fire({
              icon: 'warning',
              title: 'Warning!',
              text: 'Employee ID is exisiting!',
              showConfirmButton: true,
              timer: 2000
          })
    }
});

$("#updateaims").click(function(){
    $("#employeelist").html(loading_img);
    $.ajax({
        url : $("#site_url").val() + "/main/aimsupdate",
        success : function(msg){
            Swal.fire({
                          icon: 'success',
                          title: 'Success!',
                          text: msg,
                          showConfirmButton: true,
                          timer: 1000
                      })
            setTimeout(function(){ location.reload(); }, 2000);
        } 
    });
});

$("input[name='status'").change(function(){
    var active = $(this).val();
    loadEmplistTable(active);
});

$("input[name='employeeid']").on("change", function(){
    $.ajax({
        url: $("#site_url").val() + "/employee_/isEmployeeIDExist",
        type: "POST",
        data: { employeeid : $(this).val() },
        success:function(response){
            if(response > 0){
                iscontinue = true;
                $("input[name='employeeid']").css("border-color", "red");
                $("#warning").show();
            }
            else{
                iscontinue = false;
                $("#warning").hide();
                $("input[name='employeeid']").css("border-color", "black");
            }
        }
    });
});

// function getEmployeeList(active){
//     $.ajax({
//         url:  $("#site_url").val() + "/employee_/getEmployeeList",
//         type: "POST",
//         data:{active:active},
//         success:function(response){
//             $("#empList").html(response);
//         }
//     });
// }

var ulist;
var empStatus;
$(document).ready(function(){
    $('input[name="status"]:checked').each(function(){
        empStatus = $(this).val();
    });
    // loadEmplistTable(empStatus);
});

function loadImageAndRemarks(){
    $(".imageDiv").each(function(){
        var id = $(this).attr("imagediv");
        var gender = $(this).attr("usergender");
        var age = $(this).attr("userage");
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + "/employee_/loadImage",
            data: {id:id, gender:gender, age:age},
            success:function(res){
                $("#img_"+id).html(res);
                loadRemarks(id);
                loadStatus();
                loadCampus();
            }
        })
    })
}

function loadRemarks(id){
    $.ajax({
        type: "POST",
        url: $("#site_url").val() + "/employee_/loadRemarks",
        data: {id:id},
        success:function(res){
            $("#rem_"+id).html(res);
            
        }
    })
}

function loadEmplistTable(empStatus){
    $('#user_datatable').DataTable().destroy();
    ulist = $('#user_datatable').DataTable({
        "pagination": "number",
        "Processing": true,
        "sAjaxSource": $("#site_url").val() + "/employee_/loadEmployeeList",
        "fnServerParams" : function(aoData){
            aoData.push({"name":"empStatus", "value":empStatus});
        },
        "iDisplayLength": 5,
        "asSorting": [[ 1, "asc" ]],
        "aoColumns": [
            { "bSortable": true, }
        ],
        "sServerMethod": "POST",
        "drawCallback": function(settings) {
           loadImageAndRemarks();
        }
    });
}

$('#201filter').on('change',function(){
    var campus = GibberishAES.enc($('#campus').val(), toks);
    var teachingType = GibberishAES.enc($('#teachingType').val(), toks);
    var office = GibberishAES.enc($('#office').val(), toks);
    var department = GibberishAES.enc($('#department').val(), toks);
    var status = GibberishAES.enc($('#status').val(), toks);
    var employeeid = GibberishAES.enc($('#employeeid').val(), toks);
    $.ajax({
        type : "POST",
        url: $("#site_url").val() + "/employee_/load201sort",
        data: {campus: campus, teachingType:teachingType, department:department, status:status, office:office, employeeid:employeeid,toks:toks},
        success: function(data){
            $("select[name='employeeid']").html(data).trigger("chosen:updated");
        }
    });
});

$("#search").on("click", function(){
    $(".user_datatable").show()
    loadEmplistTableSort();
});

function loadEmplistTableSort(){
    $('#user_datatable').DataTable().destroy();
    var campus = GibberishAES.enc($('#campus').val(), toks);
    var teachingType = GibberishAES.enc($('#teachingType').val(), toks);
    var office = GibberishAES.enc($('#office').val(),toks);
    var department = GibberishAES.enc($('#department').val(), toks);
    var status = GibberishAES.enc($('#status').val(), toks);
    var employeeid = GibberishAES.enc($('#employeeid').val(), toks);
    var empstat = GibberishAES.enc($('#empstat').val(), toks);
    ulist = $('#user_datatable').DataTable({
        "pagination": "number",
        "Processing": true,
        "sAjaxSource": $("#site_url").val() + "/employee_/loadEmployeeList",
        "fnServerParams" : function(aoData){
            aoData.push({"name":"toks", "value":toks});
            aoData.push({"name":"campus", "value":campus});
            aoData.push({"name":"status", "value":status});
            aoData.push({"name":"teachingType", "value":teachingType});
            aoData.push({"name":"department", "value":department});
            aoData.push({"name":"office", "value":office});
            aoData.push({"name":"employeeid", "value":employeeid});
            aoData.push({"name":"empstat", "value":empstat});
        },
        "iDisplayLength": 5,
        "asSorting": [[ 1, "asc" ]],
        "aoColumns": [
        { "bSortable": true, }
        ],
        "sServerMethod": "POST",
        "drawCallback": function(settings) {
         loadImageAndRemarks();
         
     }
 });
}

function loadStatus(){
    $(".status").each(function(){
        var id = $(this).attr("empid");
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + "/employee_/loadStatus",
            data: {id:id},
            success:function(res){
                $("#status_"+id).html(res);
            }
        })
    })
}

function loadCampus(){
    $(".campus").each(function(){
        var id = $(this).attr("empid");
        $.ajax({
            type: "POST",
            url: $("#site_url").val() + "/employee_/loadCampus",
            data: {id:id},
            success:function(res){
                $("#campus_"+id).html(res);
            }
        })
    })
}

$('input[name="status"]').click(function(){
    loadEmplistTable($(this).val());
})

$("#user_datatable").delegate("div", "click", function() {
    if($(this).attr("employeeid")){
        var form_data = {
            job : "edit",
            employeeid : $(this).attr("employeeid"),
            view: "employee/personal_info"
        }; 
        $.ajax({
            url : $("#site_url").val() + "/main/siteportion",
            type: "POST",
            data: form_data,
            success: function(msg){
                $("#content").html(msg);
            }
        });
    }
});

$("#department").change(function(){
    $.ajax({
        url : $("#site_url").val() + "/setup_/getOffice",
        type: "POST",
        data: {department:$(this).val()},
        success: function(msg){
            $("#office").html(msg).trigger("chosen:updated");
        }
    });
});

$("input[name='tnt']").change(function() {
    $("input[name='tnt']").not(this).attr('checked', false);    
}); 

$(".chosen-select").chosen();

