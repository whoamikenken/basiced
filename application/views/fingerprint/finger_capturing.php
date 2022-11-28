<?php 
    $CI =& get_instance();
    $CI->load->model('utils');
    $rfid = Globals::_e($CI->utils->getEmployeeCode($employeeid));
?>
<div class="col-md-12">
  <div class="col-md-3">
    <input type="button" class="btn btn-primary" id="CaptureBtn" value="Capture" <?= ($rfid)? "":"disabled"?>>
    <input type="button" class="btn btn-default" id="CancelBtn" value="Cancel" onclick="reset()"><br><br>
    <input type="text" name="rfid" id="rfid" class="form-control" placeholder="RFID Tag" value="<?= $rfid; ?>">
  </div>
  <div class="col-md-6">
    <center>
      <h1 id="result"></h1>
    </center><br>
  </div>
</div>
<div class="col-md-12" style="margin-top: 5%">
  <div class="col-md-2 col-md-offset-1">
    <div id="image1" name="THUMB" fingerNum="21"></div>
  </div>
  <div class="col-md-2">
    <div id="image2" name="INDEX" fingerNum="22"></div>
  </div>
  <div class="col-md-2">
    <div id="image3" name="MIDDLE" fingerNum="23"></div>
  </div>
  <div class="col-md-2">
    <div id="image4" name="RING" fingerNum="24"></div>
  </div>
  <div class="col-md-2">
    <div id="image5" name="PINKY" fingerNum="25"></div>
  </div>
</div><br>
<script>
var toks = hex_sha512(" ");
$(document).ready(function () {
  pictures("<?= $rfid ?>");
});

function pictures(rfid) {
  if (rfid) {
    var form_data = {
      toks:toks,
      employeeid: GibberishAES.enc("<?= $employeeid ?>", toks),
      rfid: GibberishAES.enc(rfid, toks)
    };
    $.ajax({
      url: $("#site_url").val() + "/fingerprint_/getEmployeeBioPicture",
      type: "POST",
      data: form_data,
      dataType: "json",
      success: function (msg) {
        if (msg.THUMB) {
          $("#image1").html('<img class="animated fadeIn" style="width: 100%;" src="data:image/jpeg;base64,' + msg.THUMB.template + '"/>');
        }
        if (msg.INDEX) {
          $("#image2").html('<img class="animated fadeIn" style="width: 100%;" src="data:image/jpeg;base64,' + msg.INDEX.template + '"/>');
        }
        if (msg.MIDDLE) {
          $("#image3").html('<img class="animated fadeIn" style="width: 100%;" src="data:image/jpeg;base64,' + msg.MIDDLE.template + '"/>');
        }
        if (msg.PINKY) {
          $("#image4").html('<img class="animated fadeIn" style="width: 100%;" src="data:image/jpeg;base64,' + msg.PINKY.template + '"/>');
        }
        if (msg.RING) {
          $("#image5").html('<img class="animated fadeIn" style="width: 100%;" src="data:image/jpeg;base64,' + msg.RING.template + '"/>');
        }
      }
    });
  }
}

$("#rfid").focusout(function () {
  $("#CaptureBtn").prop('disabled', false);
});

function reset() {
  var form_data = {
    toks:toks,
    employeeid: GibberishAES.enc("<?= $employeeid ?>", toks),
    view: GibberishAES.enc("config/finger_capturing", toks)
  };
  $.ajax({
    url: $("#site_url").val() + "/main/siteportion",
    type: "POST",
    data: form_data,
    success: function (msg) {
      $("#tableEmp").html(msg);
    }
  });
}

$("#CaptureBtn").click(function () {
  $("#rfid").prop('disabled', true);

  $("#result").html("");
    GetTemplate();
});

$(document).ready(function () {

  var increment = 0;
  var img = "";

            // test if the browser supports web sockets
            if ("WebSocket" in window) {
                connect("ws://127.0.0.1:21187/fps");
            } else {
                $("#result").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Driver Error");
            };

            // function to send data on the web socket
            function ws_send(str) {
                try {
                    ws.send(str);
                } catch (err) {
                    $("#result").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Driver Error");
                }
            }

            // connect to the specified host
            function connect(host) {
                $("#result").html("Connecting to "+ host +"")
                try {
                    ws = new WebSocket(host); // create the web socket
                } catch (err) {
                    $("#result").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Connection Error");
                }

                ws.onopen = function () {
                    $("#result").html("Connection Success");
                };

                ws.onmessage = function (evt) {
                    var obj = eval("("+evt.data+")");
                    var status = document.getElementById("#result");

                    switch (obj.workmsg) {
                        case 1:
                        $("#result").html("Please Open Device");
                            break;
                        case 2:
                        $("#result").html("Place Finger");
                            break;
                        case 3:
                        $("#result").html("Lift Finger");
                            break;
                        case 5:
                            setTimeout(function () {
                                    $("#image" + increment).html('<img class="animated fadeIn" style="width: 100%;" src="' + img + '"/>');
                                    var form_data = {
                                        toks:toks,
                                        userID: GibberishAES.enc("<?= $employeeid ?>", toks),
                                        template: GibberishAES.enc(img, toks),
                                        feature: GibberishAES.enc(obj.data1, toks),
                                        rfid: GibberishAES.enc($("#rfid").val(), toks),
                                        finger: GibberishAES.enc($("#image" + increment).attr("name"), toks),
                                        fingerNum: GibberishAES.enc($("#image" + increment).attr("fingerNum"), toks)
                                    };
                                    $.ajax({
                                        url: $("#site_url").val() + "/fingerprint_/saveBio",
                                        type: "POST",
                                        data: form_data,
                                        success: function (msg) {
                                            (msg);
                                            Swal.fire({
                                            icon: 'success',
                                            title: 'Fingerprint Captured',
                                            text: msg,
                                            showConfirmButton: true,
                                            timer: 1000
                                          })
                                        }
                                    });
                                }, 100);

                                increment++;
                                if (increment < 5) {
                                    setTimeout(function () {
                                        $("#CaptureBtn").click();
                                    }, 2000);
                                } else {
                                    setTimeout(function () {
                                        Swal.fire({
                                        icon: 'info',
                                        title: 'Fingerprint Registration',
                                        text: "Biometrics Has Been Saved!",
                                        showConfirmButton: true,
                                        timer: 1500
                                      })
                                    }, 2000);
                                    setTimeout(function () {
                                        getEmployeeList();
                                    }, 5000);
                                }
                        break;
                        case 7:
                            if (obj.image == "null") {
                            }else{
                                console.log(obj);
                                img = "data:image/png;base64,"+ obj.image;
                                
                            }
                            break;
                    }
                };

                ws.onclose = function () {
                    $("#result").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Connection Closed!");
                };
            };

        });

        function GetTemplate() {
            try {
                //ws.send("capture");
                var cmd = "{\"cmd\":\"capture\",\"data1\":\"\",\"data2\":\"\"}";
                ws.send(cmd);
            } catch (err) {
            }

            $("#result").html("Please Place Finger");
        }
</script>