// dropdownCampus();
var toks = hex_sha512(" ");
var refresh_recompute = true;
var category = "";
var loading = $("#loading").html(); /*loading gif*/
$("input[name='category']").unbind('click').click(function() {
    $("input[name='category']").not(this).prop('checked', false);
    // if (this.value == "campus") {
    //     $("#campus").show();
    //     $("#departments").hide();
    // } else if (this.value == "department") {
    //     $("#departments").show();
    //     $("#campus").hide();
    // } else {
    //     $("#campus").hide();
    //     $("#departments").hide();
    // }
});

$("#payrollrepReport").click(function() {
    var cancontinue = true;
    // $("input[name='category']").prop('checked', false);
    $(".checked-default").prop('checked', true);
    // $("#campus").show();
    var params = "?form=attendancereport";
    params += "&cdate=" + $("#cutoff").val();
    params += "&tnt=" + $("#tnt").val();
    params += "&employeeid=" + $("select[name=employeeid]").val();
    params += "&dept_keys=" + $("select[name=office]").val();

    if (!$("#cutoff").val()) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Cut-off is required.',
            showConfirmButton: true,
            timer: 1000
        });
        cancontinue = false;
    }

    if (cancontinue) {
        $(".category").show();

        $("input[name='cutoff_date']").val($("select[name='cutoff']").val());
        $("input[name='teaching_type']").val($("select[name=tnt]").val());
        $("input[name='empid_list']").val($("select[name=employeeid]").val());
        $("input[name='sortby']").val($("input[name=category]:checked").val());
        $("input[name='dept_keys']").val($("select[name=deptid]").val());
        $("input[name='office_keys']").val($("select[name=office]").val());
        $("input[name='empstat_']").val($("select[name=empstat]").val());

        $("#frm-print").attr("target", "_blank");
        $("#frm-print").attr("action", $("#site_url").val() + "/forms/showAttendanceCutOffReport");
        $("#frm-print").attr("method", "post");
        $("#frm-print").submit();
    }

});


$("#payrollrepIndividual").click(function() {

    if ($("select[name=employeeid]").val() == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Select a employee first.',
            showConfirmButton: true,
            timer: 1000
        });
        return false;
    } else {
        if ($("#cutoff").val() == "") {
            Swal.fire({
                icon: 'warning',
                title: 'Warning!',
                text: 'Select a cut-off first.',
                showConfirmButton: true,
                timer: 1000
            });
            return false;
        } else {
            $("#cutofflist").show().html(loading);
            printIndividualReport();
        }
    }
});

$("#payrollrep").click(function() {
    var func = "";
    if ($("#tnt").val() == "teaching") func = "/attendance_/loadCutoffAttendance_Summary";
    else func = "/attendance_/validateAttendanceConfirmedViewing";
    $("#cutoffMsg").html("");
    if ($("#cutoff").val() == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Select a cut-off first.',
            showConfirmButton: true,
            timer: 1000
        });
        $('#cutoffMsg').html('Please select cutoff.');
        return false;
    } else {
        $("#cutofflist").show().html(loading);
        $.ajax({
            url: $("#site_url").val() + func,
            type: "POST",
            data: {
                toks:toks,
                tnt: GibberishAES.enc($("#tnt").val(), toks),
                cutoff: GibberishAES.enc($("#cutoff").val(), toks),
                campus: GibberishAES.enc($("#campus").val(), toks),
                deptid: GibberishAES.enc($("#deptid").val(), toks),
                office: GibberishAES.enc($("#office").val(), toks),
                empstat: GibberishAES.enc($("#empstat").val(), toks),
                employeeid: GibberishAES.enc($("select[name='employeeid']").val(), toks)
            },
            success: function(response) {
                if (response) $("#cutofflist").html(response);
                else $("#cutofflist").html("<legend><b>No attendance confirmed for selected cut-off. </b></legend>");
            }
        });
    }
});

$("#confirmrep").click(function() {
    var cancontinue = true;
    if ($("#cutoff").val() == "") {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Select a cut-off first.',
            showConfirmButton: true,
            timer: 1000
        });
        return;
    }
    $.ajax({
        url: $("#site_url").val() + "/employeemod_/fileconfig",
        type: "POST",
        data: {
            toks:toks,
            cdate: GibberishAES.enc($("#cutoff").val(), toks),
            folder: GibberishAES.enc("employeemod", toks),
            view: GibberishAES.enc("viewempsetup", toks),
            tnt: GibberishAES.enc($("#tnt").val(), toks)
        },
        success: function(msg) {
            $("#myModalatt").modal('toggle');
            $("#myModalatt").html(msg);
        }
    });
});

$("#butt_report").click(function() {
    $.ajax({
        url: $("#site_url").val() + "/reports_/showDetailedAttendanceSetup",
        type: "POST",
        data: { 
            toks: toks,
            cdate: GibberishAES.enc($("#cutoff").val(), toks) 
        },
        success: function(msg) {
            $('#loading').hide();
            $("#cutofflist").html(msg);
        }
    });
    return false;
});


$("#butt_displayLackInOut").click(function() {
    $("#cutofflist").show().html(loading);
    loadLackInOut('ABSENT');
});

$(".chosen").chosen();

$("#tnt").change(function() {
    loadempopt($(this).val(), $("#campus").val(),$("#office").val(), $("#empstatus").val(), $("select[name='deptid']").val(), $("select[name='empstat']").val());
});

$("#campus").change(function() {
    loadempopt($("#tnt").val(), $(this).val(),$("#office").val(), $("#empstatus").val(), $("select[name='deptid']").val(), $("select[name='empstat']").val());
});

$("#office").change(function() {
    loadempopt($("#tnt").val(),$("#campus").val(), $(this).val(), $("#empstatus").val(), $("select[name='deptid']").val(), $("select[name='empstat']").val());
});

$("#empstatus").change(function() {
    loadempopt($("#tnt").val(),$("#campus").val(), $("#office").val(), $(this).val(), $("select[name='deptid']").val(), $("select[name='empstat']").val());
});

$("select[name='deptid']").change(function() {
    loadempopt($("#tnt").val(),$("#campus").val(), $("#office").val(), $("#empstatus").val(), $(this).val(), $("select[name='empstat']").val());
});

$("select[name='empstat']").change(function() {
    loadempopt($("#tnt").val(),$("#campus").val(), $("#office").val(), $("#empstatus").val(), $("select[name='deptid']").val(), $(this).val());
});

$("#btn-overtime-report").unbind("click").click(function() {
    if (!$("#cutoff").val()) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Select a cut-off first.',
            showConfirmButton: true,
            timer: 1000
        });
        return;
    }

    $("#frm-process-dtr").attr("target", "_blank");
    $("#frm-process-dtr").attr("action", $("#site_url").val() + "/reports_/showOTReport");
    $("#frm-process-dtr").attr("method", "post");
    $("#frm-process-dtr").submit();
});

$("#btn-attendace-report").unbind("click").click(function() {
    $.ajax({
        url: $("#site_url").val() + "/reports_/reportconfig",
        type: "POST",
        data: {
            toks:toks,
            report: GibberishAES.enc("rfilter", toks),
            rtype: GibberishAES.enc($(this).attr("rtype"), toks),
        },
        success: function(msg) {
            $("#open-modal").click();
            $("#myModal").html(msg);
        }
    });
});

$("#btn-leave-report").unbind("click").click(function() {
    if (!$("#cutoff").val()) {
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Select a cut-off first.',
            showConfirmButton: true,
            timer: 1000
        });
        return;
    }

    $.ajax({
        url: $("#site_url").val() + "/reports_/reportconfig",
        type: "POST",
        data: {
            toks:toks,
            report: GibberishAES.enc("rfilter", toks),
            rtype: GibberishAES.enc($(this).attr("rtype"), toks),
            cutoff: GibberishAES.enc($("select[name='cutoff']").val(), toks)
        },
        success: function(msg) {
            $("#open-modal").click();
            $("#myModal").html(msg);
        }
    });
});

$("select[name='deptid']").change(function(){
    $.ajax({
        url: $("#site_url").val() + "/setup_/getOffice",
        type: "POST",
        data: {department: GibberishAES.enc( $(this).val(), toks), toks:toks},
        success: function(msg){
            $("select[name='office']").html(msg).trigger("chosen:updated");
        }
    });
});

// $("#GenerateReport").click(function() {
//     var print_report = '';
//     var cutoff = $("#cutoff").val().split(',');
//     var office = $("#office").val().split(',');
//     var dfrom = cutoff[0];
//     var dto = cutoff[1];
//     var params = "?";
//     params += "form=" + print_report;
//     params += "&datesetfrom=" + dfrom;
//     params += "&datesetto=" + dto;
//     params += "&fv=" + $("select[name='employeeid']").val();
//     params += "&edata=" + "NEW";
//     params += "&tnt=" + $("#tnt").val();
//     params += "&office=" + $("#office").val();

//     window.open($("#site_url").val() + "/attendance_/loadAttendanceReport" + params, "individual_attendance");
//     return false;
// });
function generatePDFReport(){
    var print_report = '';
    var cutoff = $("#cutoff").val().split(',');
    var office = $("#office").val().split(',');
    var dfrom = cutoff[0];
    var dto = cutoff[1];
    $("input[name='toks']").val("");
    $("input[name='datesetfrom']").val(dfrom);
    $("input[name='datesetto']").val(dto);
    $("input[name='fv']").val($("select[name='employeeid']").val());
    $("input[name='edata']").val("NEW");
    $("input[name='tnt']").val($("#tnt").val());
    $("input[name='office']").val($("#office").val());
    $("input[name='estatus']").val($("#empstat").val());

    $("#attFrm").attr("action", $("#site_url").val() + "/attendance_/loadAttendanceReport");
    $("#attFrm").attr("target", "_blank");
    $("#attFrm").attr("method", "post");
    $("#attFrm").submit();
    return false;
};

$(".chosen").chosen();

function loadLackInOut(reportType, datefrom, dateto) {
    var cutoff = $("#cutoff").val();
    var cutoff_arr = cutoff.split(',');
    var datesetfrom = '',
        datesetto = '';

    if (cutoff_arr != '') {
        $('#cutoffMsg').html('');
        datesetfrom = cutoff_arr[0];
        datesetto = cutoff_arr[1];
    } else {
        $('#cutoffMsg').html('Please select cutoff.');
        return;
    }

    $("#cmsg").prepend(loading);
    $.ajax({
        url: $("#site_url").val() + "/attendance_/loadAttendanceReport",
        type: "POST",
        data: {
            toks:toks,
            reportType: GibberishAES.enc(reportType, toks),
            datesetfrom: GibberishAES.enc(datesetfrom, toks),
            datesetto: GibberishAES.enc(datesetto, toks),
            date_filter_from: GibberishAES.enc(datefrom, toks),
            date_filter_to: GibberishAES.enc(dateto, toks),
            fv: GibberishAES.enc($('select[name=employeeid]').val(), toks),
            deptid: GibberishAES.enc($('select[name=deptid]').val(), toks),
            office: GibberishAES.enc($('select[name=office]').val(), toks),
            tnt: GibberishAES.enc($('#tnt').val(), toks),
            estatus: GibberishAES.enc($('#empstat').val(), toks),
            edata: GibberishAES.enc('NEW', toks)
        },
        success: function(msg) {
            $("#cutofflist").html(msg);
        }
    });
}

function dropdownCampus() {
    $.ajax({
        url: $("#site_url").val() + "/setup_/dropdownCampus",
        success: function(response) {
            $("#campus").html(response).trigger("chosen:updated");
        }
    });
}

function printIndividualReport() {
    var cutoff = $("#cutoff").val().split(',');
    var dfrom = cutoff[0];
    var dto = cutoff[1];
    $.ajax({
        url: $("#site_url").val() + "/process_/showallindividual",
        type: "POST",
        data: {
            toks:toks,
            datesetfrom: GibberishAES.enc(dfrom, toks),
            datesetto: GibberishAES.enc(dto, toks),
            fv: GibberishAES.enc($("select[name=employeeid]").val(), toks),
            edata: GibberishAES.enc("NEW", toks)
        },
        success: function(msg) {
            $('#cmsg').hide();
            $("#cutofflist").html(msg);
        }
    });
}

function loadempopt(etype = "", campusid = "", officeid = "", empstatus = "", deptid = "", empstat="") {
    $.ajax({
        url: $("#site_url").val() + "/process_/callemployee",
        type: "POST",
        data: {
            toks:toks,
            etype: GibberishAES.enc(etype, toks),
            campusid: GibberishAES.enc(campusid, toks),
            isactive: GibberishAES.enc(empstatus, toks),
            deptid: GibberishAES.enc(deptid, toks),
            officeid:GibberishAES.enc(officeid, toks),
            estatus:GibberishAES.enc(empstat, toks)
        },
        success: function(msg) {
            $("select[name='employeeid']").html(msg).trigger('chosen:updated');
        }
    });
}