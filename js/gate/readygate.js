$(document).ready(function () {

    $("#login-rfid")
        .on("change", function(e) {
            e.preventDefault();
            get_rfid();
            return false;
        })
        .on("click", function(e) {
            clearTimeout(animate);
            // if (template = 0) {
                $("#tblrecords").DataTable().draw();
            // }
            clear_fields();
            return false;
        });

var IDLE_TIMEOUT = 8; //seconds
var _localStorageKey = 'global_countdown_last_reset_timestamp';
var _idleSecondsTimer = null;
var _lastResetTimeStamp = (new Date()).getTime();
var _localStorage = null;
var my_time_out;
var employeeid;
var employeeName;
var employeeDept;
var text;
var LR;
var ws;
var animate;

AttachEvent(document, 'click', ResetTime);
AttachEvent(document, 'mousemove', ResetTime);
AttachEvent(document, 'keypress', ResetTime);
AttachEvent(window, 'load', ResetTime);

try {
    _localStorage = window.localStorage;
} catch (ex) {}

function GetLastResetTimeStamp() {
    var lastResetTimeStamp = 0;
    if (_localStorage) {
        lastResetTimeStamp = parseInt(_localStorage[_localStorageKey], 10);
        if (isNaN(lastResetTimeStamp) || lastResetTimeStamp < 0)
            lastResetTimeStamp = (new Date()).getTime();
    } else {
        lastResetTimeStamp = _lastResetTimeStamp;
    }

    return lastResetTimeStamp;
}

function SetLastResetTimeStamp(timeStamp) {
    if (_localStorage) {
        _localStorage[_localStorageKey] = timeStamp;
    } else {
        _lastResetTimeStamp = timeStamp;
    }
}

function ResetTime() {
    SetLastResetTimeStamp((new Date()).getTime());
}

function AttachEvent(element, eventName, eventHandler) {
    if (element.addEventListener) {
        element.addEventListener(eventName, eventHandler, false);
        return true;
    } else if (element.attachEvent) {
        element.attachEvent('on' + eventName, eventHandler);
        return true;
    } else {
        //nothing to do, browser too old or non standard anyway
        return false;
    }
}

function WriteProgress(msg) {
    var oPanel = document.getElementById("SecondsUntilExpire");
    if (oPanel)
        oPanel.innerHTML = msg;
    else if (console) {
    }
}

function CheckIdleTime() {
    var currentTimeStamp = (new Date()).getTime();
    var lastResetTimeStamp = GetLastResetTimeStamp();
    var secondsDiff = Math.floor((currentTimeStamp - lastResetTimeStamp) / 1000);
    if (secondsDiff <= 0) {
        ResetTime();
        secondsDiff = 0;
    }
    WriteProgress((IDLE_TIMEOUT - secondsDiff) + "");
    if (secondsDiff >= IDLE_TIMEOUT) {
        window.clearInterval(_idleSecondsTimer);
        ResetTime();
        clear_fields();
    }
}

function clear_fields() {
    window.clearInterval(_idleSecondsTimer);
    $("#container-alert").empty().html('<div class="alert alert-success"><strong>Connected!</strong></div>');
    $("#login-rfid").empty().val("").focus();
    $("input[name='fnctr'], input[name='fnlizt']").remove();
    for (var i = 0; i <= 10; i++) {
        if ($("input[name='S" + i + "']").length) {
            $("input[name='S" + i + "']").remove();
        }
        if ($("input[name='M" + i + "']").length) {
            $("input[name='M" + i + "']").remove();
        }
    }

    animate = setTimeout(function(){
        $("#resultDiv").addClass("animated").addClass("zoomOut").removeClass("zoomIn");
        $("#container-response").empty().css("color", "black").html("Good day, please sign in");
        $('#img-profile').attr("src", $("#base_url").val() + "/images/school_logo.png");
        setTimeout(function(){ $("#resultDiv").html(""); }, 500);
    }, 3500);
}

function get_result() {
    var seconds = $("#sec").text();
    var minutes = $("#min").text();
    var hours = $("#hours").text();  
    $("#ChkIn").val(hours+":"+minutes+":"+seconds+$("#periods").text());

    var form_data = {
        EmpID: employeeid,
        exemp: "exemp",
        ChkIn: $("input[name='ChkIn']").val(),  
        dvid: $("#privateip").val() 
    };

    $.ajax({
        url: $("#site_url").val() + "/fingerprint_/resultChecker",
        type: "POST",
        data: form_data, 
        success: function(response)
        {
            doCheckIdleTimeAfterGetRFID(false);
            if (response == "wait") {
                $("#container-response").empty().css("color","red").html("<h2><b>Oh snap!</b> Please wait 30 seconds login aborted.</h2>");
            }else{
                $("#container-response").css("color", "#fff").html(text);
                $("#resultDiv").css("background-color", "#ffe75e").css("color", "black").html("<br><h2>Successfully Logged "+ LR +"</h2><h1>Employee: "+ employeeName +"</h1><h4>Department: "+ employeeDept +"</h4><br>");
                $("#resultDiv").removeClass("zoomOut").addClass("animated").addClass("zoomIn");
            }
            setTimeout(function(){ 
                $("#login-rfid").click(); 
            }, 500);
        }
    });
}

function doCheckIdleTimeAfterGetRFID(is_continue = true) {
    clearTimeout(my_time_out);
    if (is_continue) {
        my_time_out = setTimeout(function() {
            clear_fields();
        }, 8000);
    }
}

function get_rfid() {
    $("#container-response").empty().html('<center><i class="fa fa-spinner"></i></center>');
    $.ajax({
        url: $("#site_url").val() + "/fingerprint_/rfidChecker",
        type: "POST",
        data: {
            tmp: $("#login-rfid").val(),
            rfid: hex_sha512($("#login-rfid").val()),
            dvid: $("#privateip").val()
        },
        dataType: "json",
        success: function(response) {

            employeeid = response["EmpID"];
            employeeName = response["Fullname"];
            employeeDept = response["Dept"];
            LR = response["LR"];
            text = response["text"];

            $("#id").val(response.id);
            if (response['stat'] == 1){
                $("#container-response").css("color", "white").html(response["text"]);
            }else if(response['stat'] == 0){
                $("#container-response").css("color", "red").html(response["text"]);
            }

            if (response['Image']) {
                $('#img-profile').attr("src", response['Image']);
            } else {
                $('#img-profile').attr("src", $("#base_url").val() + "/images/school_logo.png");
            }

            $("#Fullname").val(response["Fullname"]);
            $("#EmpID").val(response["EmpID"]);

            var seconds = $("#sec").text();
            var minutes = $("#min").text();
            var hours = $("#hours").text();
            $("#ChkIn").val(hours + ":" + minutes + ":" + seconds + $("#periods").text());
            $('<input>').attr({
                type: 'hidden',
                id: "fnctr",
                name: "fnctr",
                value: 1
            }).appendTo('#form-gate');

            switch (response['stat']) {
                case 1:
                    GetTemplate();
                    break;
                case 2:
                    get_result();
                    break;
                default:
                    $("#login-rfid").val("").focus();
                    break;
            }

            doCheckIdleTimeAfterGetRFID();
        },
        error: function(response) {
            $("#container-response").css("color", "red").html("<b>Warning!</b> No internet connection. ");
        }
    });
}

// function to send data on the web socket
function ws_send(str) {
    try {
        ws.send(str);
    } catch (err) {
        // $("#container-response").empty().css("color", "red").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Driver Error");
    }
}

function GetTemplate() {

    // test if the browser supports web sockets
    if ("WebSocket" in window) {
        connect("ws://127.0.0.1:21187/fps");
    } else {
        // $("#container-alert").empty().html('<div class="alert alert-success"><strong>Connected!</strong></div>');
    };

    try {
        //ws.send("capture");
        var cmd = "{\"cmd\":\"capture\",\"data1\":\"\",\"data2\":\"\"}";
        ws.send(cmd);
    } catch (err) {
    }
    $("#container-response").empty().css("color", "black").html("Please Place Finger");
}

// connect to the specified host
function connect(host) {
    $("#resultTest").html("Connecting to " + host + "")
    try {
        ws = new WebSocket(host); // create the web socket
    } catch (err) {
        // $("#container-response").empty().css("color", "red").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Driver Error");
    }

    ws.onopen = function() {
        $("#container-alert").empty().html('<div class="alert alert-success"><strong>Connection Success</strong></div>');
    };

    ws.onmessage = function(evt) {
        var obj = eval("(" + evt.data + ")");
        switch (obj.workmsg) {
            case 1:
                $("#resultTest").html("Please Open Device");
                break;
            case 2:
                $("#resultTest").html("Place Finger");
                break;
            case 3:
                $("#resultTest").html("Lift Finger");
                break;
            case 7:
                if (obj.image == "null") {} else {
                    setTimeout(function() {
                        var form_data = {
                            "subject": obj.image,
                            "control": "find",
                            "id": $("#id").val()
                        };
                        $.ajax({
                            type: "POST",
                            url: "http://localhost:80/api/Identify",
                            data: JSON.stringify(form_data),
                            contentType: "application/json",
                            dataType: "json",
                            success: function(msg) {
                                if (msg.user != "None") {
                                    var seconds = $("#sec").text();
                                    var minutes = $("#min").text();
                                    var hours = $("#hours").text();
                                    $("#ChkIn").val(hours + ":" + minutes + ":" + seconds + $("#periods").text());

                                    var form_data = {
                                        EmpID: msg.user,
                                        ChkIn: $("input[name='ChkIn']").val(),
                                        dvid: getDeviceId()
                                    };

                                    $.ajax({
                                        url: $("#site_url").val() + "/fingerprint_/resultChecker",
                                        type: "POST",
                                        data: form_data,
                                        success: function(response) {
                                            doCheckIdleTimeAfterGetRFID(false);
                                            if (response == "wait") {
                                                $("#container-response").empty().css("color", "red").html('Please wait a minute login aborted');
                                            }else{
                                                $("#container-response").empty().css("color", "#ffe75e").html('Well done!</b> You have successfully logged');
                                                $("#resultDiv").css("background-color", "#ffe75e").html("<br><h2>Successfully Logged "+ LR +"</h2><h1>Employee: "+ employeeName +"</h1><h4>Department: "+ employeeDept +"</h4><br>");
                                                $("#resultDiv").removeClass("zoomOut").addClass("animated").addClass("zoomIn");
                                            }
                                            $("#login-rfid").click();
                                        }
                                    });
                                }else {
                                    $("#resultDiv").css("background-color", "red").html("<br><h2><b>Oh snap!</b></h2><h1>Employee: "+ employeeName +"</h1><h4>Department: "+ employeeDept +"</h4><br>");
                                    $("#resultDiv").removeClass("zoomOut").addClass("animated").addClass("zoomIn");
                                    $("#login-rfid").click();
                                }
                            }
                        });
                    }, 5);
                }
                break;
        }
    };

    ws.onclose = function() {
        // $("#container-response").empty().css("color", "red").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Driver Error");
    };
};
});