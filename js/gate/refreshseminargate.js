(function($) {
    'use strict';

    var template = 1;
    $(document).ready(function() {
        getLocalIP().then((privateip) => {
            loadmodal();
            getTerminalUsername(privateip);
            $("input[name='privateip']").remove();

            $('<input>').attr({
                type: 'hidden',
                id: 'privateip',
                name: 'privateip',
                value: privateip
            }).appendTo('#form-gate');

            $.ajax({
                url: $("#site_url").val() + "/fingerprint_/set_terminal_id",
                type: "POST",
                data: {
                    privateip: $("input[name='privateip']").val(),
                    dvid: getDeviceId()
                },
                dataType: "json",
                success: function(response) {
                    // console.log(response); {is_logged_in: true, current_ip: "192.168.8.228"} 
                    switch (true) {
                        case !response['is_logged_in']:
                            $("#modal-view").modal({
                                backdrop: 'static',
                                keyboard: false
                            });
                            setTimeout(function() {
                                $("#check_username").focus();
                            }, 1000);
                            break;
                        default:
                            if (response['template'] == "1") {
                                $("#tblrecords_wrapper").hide();
                                template = 1;
                            }else if(response['template'] == "0"){
                                $("#resultDiv").hide();
                                template = 0;
                            }
                            syncfinger();
                            setInterval(function() {
                                if ($("#login-rfid").val() == "") {
                                    $("#login-rfid").focus();
                                }
                            }, 1000);
                            break;
                    }
                }
            });
            return false;
        });

        // Create two variable with the names of the months and days in an array
        var monthNames = ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"];
        var dayNames = ["Sun", "Mon", "Tue", "Wed", "Thur", "Fri", "Sat"];

        // Create a newDate() object
        var newDate = new Date();
        // Extract the current date from Date object
        newDate.setDate(newDate.getDate());
        // Output the day, date, month and year    
        $('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

        var today = newDate.getFullYear() + "-" + (newDate.getMonth() + 1) + "-" + newDate.getDate();
        var username = $("#terminal-name").text();

        setInterval(function() {
            // Create a newDate() object
            var newDate = new Date();
            // Extract the current date from Date object
            newDate.setDate(newDate.getDate());
            // Output the day, date, month and year    
            $('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

            today = newDate.getFullYear() + "-" + (newDate.getMonth() + 1) + "-" + newDate.getDate();
        }, 1000);

        setInterval(function() {
            // Create a newDate() object and extract the seconds of the current time on the visitor's
            var seconds = getServerTime(server_unixtime, "seconds");
            // Add a leading zero to seconds value
            $("#sec").html((seconds < 10 ? "0" : "") + seconds);
            server_unixtime++;
        }, 1000);

        setInterval(function() {
            // Create a newDate() object and extract the minutes of the current time on the visitor's
            var minutes = getServerTime(server_unixtime, "minutes");
            // Add a leading zero to the minutes value
            $("#min").html((minutes < 10 ? "0" : "") + minutes);
        }, 1000);

        setInterval(function() {
            // Create a newDate() object and extract the hours of the current time on the visitor's
            var hours = getServerTime(server_unixtime, "hours");
            var dd = "AM";
            var h = hours;
            if (h >= 12) {
                h = hours - 12;
                dd = "PM";
            }
            if (h == 0) {
                h = 12;
            }
            // Add a leading zero to the hours value 
            $("#hours").html((h < 10 ? "0" : "") + h);

            $("#periods").html(dd);
        }, 1000);

        getLocalIP().then((privateip) => {
            var oTable = $("#tblrecords").DataTable({
                processing: true,
                serverSide: true,
                ajax: {
                    url: $("#site_url").val() + "/fingerprint_/attempts_ajax_list",
                    data: function(data) {
                        data.today = today
                        data.ip = privateip
                    }
                },
                "drawCallback": function(settings) {
                    $('#tblrecords tr').each(function() {
                        if ($(this).find("td:eq(3)").text() == "OUT") {
                            $(this).css("background-color", "#FFB6B6");
                        } else if ($(this).find("td:eq(3)").text() == "IN") {
                            $(this).css("background-color", "#004d40");
                        }

                    });

                },
                deferRender: true,
                searching: false,
                pageLength: 12,
                lengthChange: false,
                "bInfo": false,
                order: [
                    [0, "desc"],
                    [1, "desc"]
                ]
            });
        });

    });

})(window.jQuery);

var uuid = function() {
    var u = 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g,
        function(c) {
            var r = Math.random() * 16 | 0,
                v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    return u;
}

var getDeviceId = function() {
    var current = window.localStorage.getItem("_PGATEWAYID_")
    if (current) return current;
    var id = uuid();
    window.localStorage.setItem("_PGATEWAYID_", id);
    return id;
}

function loadmodal(hid = '', hjob = 'Add', htitle = '', htarget = '#modal-view') {
    $.ajax({
        url: $("#site_url").val() + "/fingerprint_/check_login",
        type: "POST",
        data: {
            'hjob': hjob,
            'htarget': htarget
        },
        success: function(response) {
            $(htarget + " .modal-title").html("Please log-in");
            $(htarget + " .modal-footer").html('<button type="button" class="btn btn-primary btnsubmit">Login</button>');
            $(htarget + " .modal-body").html(response);
        }
    });
    return false;
}

function getTerminalUsername(privateip) {
    $.ajax({
        url: $("#site_url").val() + "/fingerprint_/get_terminal_username",
        type: "POST",
        data: {
            privateip: privateip
        },
        success: function(response) {
            $("#terminal-name").text("Terminal : " + response);
        }
    });
}

function getServerTime(unix_timestamp, categ) {
    var date = new Date(unix_timestamp * 1000);
    // Hours part from the timestamp
    var hours = date.getHours();
    // Minutes part from the timestamp
    var minutes = "" + date.getMinutes();
    // Seconds part from the timestamp
    var seconds = "" + date.getSeconds();

    // Will display time in 10:30:23 format
    hours + ':' + minutes.substr(-2) + ':' + seconds.substr(-2);
    if (categ == "seconds") return seconds.substr(-2);
    else if (categ == "minutes") return minutes.substr(-2);
    else return hours;
}

function getIpAddressPHP() {
    var ip_add = "";
    $.ajax({
        url: $("#site_url").val() + "/fingerprint_/getIpAddressPHP",
        type: "POST",
        async: false,
        success: function(response) {
            ip_add = response;
        }
    });
    return ip_add;
}

function syncfinger() {
    $("#login-rfid").attr('disabled', 'disabled');
    $('#container-error').empty().html('<center><i class="fa fa-asterisk fa-2x fa-spin text-primary"></i><br><h3></h3></center>');
    $("#container-response").empty().css("color", "black").html("Synchronizing Please Wait");
    $.ajax({
        type: "POST",
        url: $("#site_url").val() + "/fingerprint_/syncfingers",
        data: {},
        contentType: "application/json",
        dataType: "json",
        success: function(msg) {
            $("#container-response").empty().css("color", "black").html("SYNCING.... Getting " + msg.length + " Persons Finger Data");
            $("#personRegistered").val(msg.length);
            var reset_data = {
                "subject": "",
                "control": "reset"
            };
            $.ajax({
                type: "POST",
                url: "http://localhost:80/api/Identify",
                data: JSON.stringify(reset_data),
                contentType: "application/json",
                dataType: "json",
                complete: function(response) {
                    $("#container-response").empty().css("color", "black").html("SYNCING.... Registering " + msg.length * 5 + " Template");
                    for (var i = 0; i < msg.length; i++) {
                        var obj = msg[i];
                        var form_data = {
                            "template": obj.template,
                            "template1": obj.template1,
                            "template2": obj.template2,
                            "template3": obj.template3,
                            "template4": obj.template4,
                            "control": "add",
                            "user": obj.id
                        };
                        $.ajax({
                            type: "POST",
                            url: "http://localhost:80/api/Identify",
                            data: JSON.stringify(form_data),
                            contentType: "application/json",
                            dataType: "json",
                            success: function(data) {

                            }
                        });
                    }
                }
            });
        },
        error: function(request, status, error) {

        }
    });

    $(document).ajaxStop(function() {
        $("#login-rfid").prop('disabled', false);
        $("#container-response").empty().css("color", "black").html("Good day, Please sign in");
        $(this).unbind("ajaxStop");
    });
}