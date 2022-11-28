<?PHP
header('Access-Control-Allow-Origin: *');
?>
<div class="col-md-12">
	<div class="col-md-6">
		<input type="button" class="btn btn-primary" id="CaptureTest" value="Capture">
		<input type="button" class="btn btn-default" id="CancelTest" value="Cancel" onclick="reset()"></div>
	<div class="col-md-6">
		<h1 id="resultTest"></h1><br>
	</div>
</div>
<div class="col-md-12" style="margin-top: 5%">
  <div class="col-md-2 col-md-offset-1"><div id="image1"></div></div>
  <div class="col-md-2"><div id="image2" ></div></div>
  <div class="col-md-2"><div id="image3" ></div></div>
  <div class="col-md-2"><div id="image4" ></div></div>
  <div class="col-md-2"><div id="image5" ></div></div>
</div><br>
<script>
var toks = hex_sha512(" ");
var subject1;
var subject2;
var subject3;
var subject4;
var subject5;
var ws;

function reset() {
  getEmployeeBio();
}

function pictures() {
  var form_data = {
    toks:toks,
    employeeid : GibberishAES.enc("<?= $employeeid ?>", toks),
    rfid: GibberishAES.enc("<?= $rfid ?>", toks)
  }; 
  $.ajax({
      url : $("#site_url").val() + "/fingerprint_/getPictureCount",
      type: "POST",
      data: form_data,
      dataType: "json",
      success: function(msg){
        subject1 = msg['0'].template;
        subject2 = msg['1'].template;
        subject3 = msg['2'].template;
        subject4 = msg['3'].template;
        subject5 = msg['4'].template;
        var form_data_registration = { 
            "control": "add",
            "subject1": subject1,
            "subject2": subject2,
            "subject3": subject3,
            "subject4": subject4,
            "subject5": subject5 
        };
        $.ajax({
            type: "POST",
            url : "http://localhost:80/api/OneToFive",
            data: JSON.stringify(form_data_registration),
            contentType: "application/json",
            dataType: "json",
            success: function(msg){
            }
        });
            
      }
  });
}

$("#CaptureTest").click(function() {
  $("#rfid").prop('disabled', true);

  $("#result").html("");
  GetTemplate();
});

$(document).ready(function () {
  pictures();
  var increment = 0;
            // test if the browser supports web sockets
            if ("WebSocket" in window) {
                connect("ws://127.0.0.1:21187/fps");
            } else {
                $("#resultTest").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Driver Error");
            };

            // function to send data on the web socket
            function ws_send(str) {
                try {
                    ws.send(str);
                } catch (err) {
                    $("#resultTest").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Driver Error");
                }
            }

            // connect to the specified host
            function connect(host) {
                $("#resultTest").html("Connecting to "+ host +"")
                try {
                    ws = new WebSocket(host); // create the web socket
                } catch (err) {
                    $("#resultTest").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Connection Error");
                }

                ws.onopen = function () {
                    $("#resultTest").html("Connection Success");
                };

                ws.onmessage = function (evt) {
                    var obj = eval("("+evt.data+")");
                    var status = document.getElementById("#resultTest");
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
                            if (obj.image == "null") {
                            }else{
                                setTimeout(function(){ 
                                    var img = "data:image/png;base64,"+ obj.image;
                                    $("#image" + increment).html('<img class="animated fadeIn" style="width: 100%;" src="' + img + '"/>');
                                    var form_data = { 
                                        "subject": obj.image,
                                        "control": "find"
                                     };
                                      $.ajax({
                                          type: "POST",
                                          url : "http://localhost:80/api/OneToFive",
                                          data: JSON.stringify(form_data),
                                          contentType: "application/json",
                                          dataType: "json",
                                          success: function(msg){
                                              var percentage = "0%";
                                              if (msg.score > 100) {
                                                percentage = "100%";
                                              }else{
                                                percentage = msg.score+"%";
                                              }
                                              $("#percentage"+ increment).text(percentage)
                                              if (msg.user == "True") {
                                                Swal.fire({
                                                  icon: 'success',
                                                  title: 'Valid Fingerprint',
                                                  text: 'Match percentage is '+ percentage,
                                                  showConfirmButton: true,
                                                  timer: 1500
                                                })
                                              }else{
                                                Swal.fire({
                                                  icon: 'error',
                                                  title: 'Oops...',
                                                  text: 'Your fingerprint is not valid!',
                                                  timer: 1500
                                                })
                                              }
                                          }
                                      });
                                 }, 500);

                                increment++;
                                if (increment < 5) {
                                  setTimeout(function(){ 
                                    $("#CaptureTest").click();
                                  }, 1000);
                                }else{
                                  setTimeout(function(){ 
                                    reset();
                                  }, 3000);
                                }
                            }
                            break;
                    }
                };

                ws.onclose = function () {
                    $("#resultTest").html("ERROR PLEASE COORDINATE WITH DEVELOPER!<br> Connection Closed!");
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

            $("#resultTest").html("Please Place Finger");
        }
</script>