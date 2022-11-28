var toks = hex_sha512(" ");
var loading = $("#loading_div").html();
$(document).ready(function(){
    getEmployeeList();
    $('.chosen').chosen();
    
    $("#datesetfrom,#datesetto").datepicker({
        autoclose: true,
        todayBtn : true
    });
});

$("select[name=teaching_type]").change(function(){
    getEmployeeList();
});

$("#attendance_report").click(function(){
  
   if($("#cutoff").val() == ""){
       Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Select a cut-off first.',
            showConfirmButton: true,
            timer: 1000
        })
       return false;
   }else if($("#tnt").val() == ""){
       Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Teaching Type is required.',
            showConfirmButton: true,
            timer: 1000
        })
       return false;
   }else{
     $("#displaylogs").show().html(loading);
      $.ajax({
        url: $("#site_url").val() + "/attendance_/validateAttendanceConfirmedViewing",
        type: "POST",
        data: {
            tnt:  GibberishAES.enc($("#tnt").val() , toks),
            cutoff: GibberishAES.enc($("#cutoff").val()  , toks),
            employeeid:  GibberishAES.enc($("select[name='employeeid']").val() , toks),
            toks:toks
        },
        success:function(response){
            if(response) $("#displaylogs").html(response);
            else $("#displaylogs").html("<legend><b>No attendance confirmed for selected cut-off. </b></legend>");
        }
      });
   
   }
});

$("#print_attendance_report").click(function(){
    $("#att_report_filter").modal("toggle");
});

$("#generate_report").click(function(){
    var cancontinue = true;
    $("#campus").show();
    var params = "?form=attendancereport";   
    params += "&cdate="+ GibberishAES.enc($("#cutoff").val() , toks);
    params += "&tnt="+GibberishAES.enc($("#tnt").val()  , toks);
    params += "&employeeid="+ GibberishAES.enc($("select[name=employeeid]").val() , toks);
    params += "&toks="+ toks;

    if (!$("#cutoff").val()) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Cut-off is required!',
            showConfirmButton: true,
            timer: 1000
        })
        cancontinue = false;
    }

    if($("#tnt").val() == ""){
       Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Teaching Type is required.',
            showConfirmButton: true,
            timer: 1000
        })
        cancontinue = false;
   }

    if (cancontinue) {
        $("input[name='cutoff_date']").val($("#cutoff").val());
        $("input[name='teaching_type']").val($("#tnt").val());
        $("input[name='empid_list']").val($("select[name=employeeid]").val());
        $("#frm-print").attr("target", "_blank");
        $("#frm-print").attr("action", $("#site_url").val() + "/forms/showAttendanceCutOffReportForEmpSide");
        $("#frm-print").attr("method", "post");
        $("#frm-print").submit();
    }

    $("input[name='category']").prop('checked',false); 
    $(".checked-default").prop('checked', true);
});

$("#individual_report").click(function(){ 
    if($("#cutoff").val() == ""){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please set a range of date first.',
            showConfirmButton: true,
            timer: 1000
        })
        return;
    }
    else if($("#tnt").val() == ""){
       Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Teaching Type is required.',
            showConfirmButton: true,
            timer: 1000
        })
        return;
   }
    else if($("select[name='employeeid']").val()==""){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Employee is required.',
            showConfirmButton: true,
            timer: 1000
        })
        $("select[name='employeeid']").focus();
        return;
    }
    $("#displaylogs").html("<span class=\"loading-notif\" id=\"loading\"> " + loading + " Loading, Please Wait...</span>");
    $.ajax({
        url: $("#site_url").val() + "/process_/showalldeptlogs",
        type: "POST",
        data: {
            cutoff:  GibberishAES.enc($("#cutoff").val() , toks),
            deptid :  GibberishAES.enc($("input[name='head']").val() , toks),
            tnt :  GibberishAES.enc($("#tnt").val() , toks),
            fv :  GibberishAES.enc($("select[name='employeeid']").val() , toks),
            toks:toks
        },
        success: function(msg){
            print_report = 'individual';
            $("#displaylogs").html(msg);
        }
    });   

    return false;
});

$("#print_batch").unbind('click').click(function(){
    var print_report = "";
    if(!$("#cutoff").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please select a Cut-Off.',
            showConfirmButton: true,
            timer: 1000
        })
        return;
    }
    else if($("#tnt").val() == ""){
       Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Teaching Type is required.',
            showConfirmButton: true,
            timer: 1000
        })
        return;
   }
    var cutoff = $("#cutoff").val().split(",");
    var params = "?";
    params += "form="+print_report;
    params += "&datesetfrom=" +  cutoff[0]; 
    params += "&datesetto=" +  cutoff[1];
    params += "&fv=" +  $("select[name='employeeid']").val();
    params += "&deptid=" +   $("input[name='head']").val();
    params += "&edata=" + "NEW";       
    params += "&tnt=" +  $("#tnt").val();
    params += "&estatus=";
    params += "&campus=" +  $("input[name='selected_campus']").val();
    params += "&toks=" + toks;
 
    window.open($("#site_url").val() + "/attendance_/loadAttendanceReport" + params,"individual_attendance");
});

$("#displayAttendanceModal").click(function(){
    // $("#print_batch_attendance").modal("toggle");
     var print_report = "";
    if(!$("#cutoff").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Please select a Cut-Off.',
            showConfirmButton: true,
            timer: 1000
        })
        return;
    }else if($("#tnt").val() == ""){
       Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Teaching Type is required.',
            showConfirmButton: true,
            timer: 1000
        })
        return;
   }
    var cutoff = $("#cutoff").val().split(",");
    var params = "?";
    params += "form="+print_report;
    params += "&datesetfrom=" +  cutoff[0]; 
    params += "&datesetto=" +  cutoff[1];
    params += "&fv=" +  $("select[name='employeeid']").val();
    params += "&deptid=" +   $("input[name='head']").val();
    params += "&edata=" + "NEW";       
    params += "&tnt=" +  $("#tnt").val();
    params += "&estatus=";
    params += "&campus=" +  $("input[name='selected_campus']").val();
    params += "&toks=" + toks;
 
    window.open($("#site_url").val() + "/attendance_/loadAttendanceReport" + params,"individual_attendance");
});

function getEmployeeList(){
    var teachingtype = $("select[name=teaching_type]").val();
    var selected_campus = $("input[name=selected_campus]").val();
    var selected_dept = $("input[name=selected_dept]").val();
    $.ajax({
        type: "POST",
        url: $("#site_url").val() + "/extensions_/getEmplistForDepartmentAttendance",
        data: {
          teachingtype: GibberishAES.enc(teachingtype , toks),
          selected_campus:  GibberishAES.enc(selected_campus , toks),
          selected_dept:  GibberishAES.enc(selected_dept , toks),
          toks:toks
        },
        success:function(response){
          $("#employeeid").html(response).trigger('chosen:updated');
        }
    });
}