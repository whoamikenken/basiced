<?PHP
header('Access-Control-Allow-Origin: *');
?>
<div class="col-md-12">
	<div class="col-md-6">
		<input type="button" class="btn btn-primary" id="CaptureBtn" value="Capture">
		<input type="button" class="btn btn-default" id="CancelBtn" value="Cancel" onclick="reset()"></div>
	<div class="col-md-6">
		<h1 id="result"></h1><br>
	</div>
</div>
<div class="col-md-12">
	<div class="col-md-2 col-md-offset-1"><canvas id="fingerframe" height="480" width="320" style="display: none;"></canvas><div id="image"></div></div>
</div><br>
<script>
	
var fpHTTSrvOpEP = "http://127.0.0.1:15170/fpoperation";
var fingerFrame = document.getElementById("fingerframe");
var lastInitOp;

function reset() {
	var form_data = {
	    employeeid : "<?= $employeeid ?>",
	    view: "config/finger_capturing"
	}; 
	$.ajax({
	    url : $("#site_url").val() + "/main/siteportion",
	    type: "POST",
	    data: form_data,
	    success: function(msg){
	        $("#tableEmp").html(msg);
	    }
	});
}

function put(url) {
  return new Promise(function(resolve, reject) {

    var req = new XMLHttpRequest();
    req.open('PUT', url);

    req.onload = function() {

      if (req.status == 200) {
        resolve(req.response);
      }
      else {
        reject(fixError(req.statusText, "Server response"));
      }
    };

    req.onerror = function() {
      reject(fixError("", "FPHttpServer not available"));
    };

    req.send();
  });
}

$("#CaptureBtn").click(function() {
    var opName = 'capture';
    var libName = 'ansisdk';
    var sendSampleNum = false;
    var sampleNum = "1";
    if(sendSampleNum) {
        sampleNum = sampleNumList.value;
        
        var checkNum = parseInt(sampleNum);
        
        if(checkNum < 3 || checkNum > 10 || sampleNum == "") {
            $("#result").html("Error");
            return;
        }
    }

    var req = JSON.stringify({operation: opName,   username: "", usedlib: libName, isoconv: "0", samplenum: sampleNum });
    $("#result").html("");

    post(fpHTTSrvOpEP, req).then(function(response) {
    		message = "Put Your Finger On The Scanner";
        $("#result").html(message);
        parseOperationDsc(JSON.parse(response));
    }).catch(function(error) {

    })
});

function linkOperationTemplate(opId, operationName) {
    var target = "/template";
    var saveAs = "template.bin"
    var resultText = "Result template"
    if ( operationName == 'capture' ) {
        target = "/image"
        saveAs = "image.bin"
        resultText = "Result image bytes"
    }
    var url = fpHTTSrvOpEP + '/' + opId + target;

    //resultLink.click()
}

function parseOperationDsc(opDsc) {
    var res = true;
    if(opDsc.state == 'done') {
        if(opDsc.status == 'success') {
            $("#result").html(opDsc.message);
                setTimeout(function(){ 
                    var canvas = document.getElementById("fingerframe");
                    var img    = canvas.toDataURL("image/jpeg");
                    $("#image").html('<img class="animated fadeIn" style="width: 100%;" src="'+img+'"/>');
                    var res = img.replace("data:image/jpeg;base64,", "");
                    var form_data = { "subject": res, "user": "<?= $employeeid ?>" };
            					$.ajax({
                          type: "POST",
            					    url : "http://localhost/Api/Authentication",
            					    data: JSON.stringify(form_data),
                          contentType: "application/json",
                          dataType: "json",
            					    success: function(msg){
            					        if (msg.user == "True") {
                                alert("True");
                                getEmployeeBio();
                              }else{
                                alert("False");
                              }
            					    }
            					});
                 }, 100);

            linkOperationTemplate(opDsc.id, opDsc.operation)
        }

        if(opDsc.status == 'fail') {
            fixError("", opDsc.errorstr)
            res = false;
            
            if(parseInt(opDsc.errornum) != -1) {
              var url = fpHTTSrvOpEP + '/' + opDsc.id;
                  return new Promise(function(resolve, reject) {

                  var req = new XMLHttpRequest();
                  req.open("DELETE", url);

                  req.onload = function() {
                    if (req.status == 200) {
                      resolve(req.response);
                    }
                    else {
                      //reject(fixError(req.statusText, "Server response"));
                    }
                  };

                  req.onerror = function() {
                    reject(
                      $("#result").html("Please Configure Scanner Driver"));
                  };

                  req.send();
                });
            }
        }
    }
    else if(opDsc.state == 'init') {
        lastInitOp = opDsc.id
        setTimeout(getOperationState, 1000, opDsc.id);
        setTimeout(getOperationImg, 1000, opDsc.id, parseInt(opDsc.devwidth), parseInt(opDsc.devheight));
    }
    else if(opDsc.state == 'inprogress')
    {
        if(opDsc.fingercmd == 'puton') {
        	message = "Put Your Finger On The Scanner";
            $("#result").html(message);
        }

        if(opDsc.fingercmd == 'takeoff') {
            $("#result").html("Take off finger from scanner");
        }

        setTimeout(getOperationState, 1000, opDsc.id);
        setTimeout(getOperationImg, 1000, opDsc.id, parseInt(opDsc.devwidth), parseInt(opDsc.devheight));
    }

    return res;
}

function getOperationImg(opId,frameWidth, frameHeight) {
    var url = fpHTTSrvOpEP + '/' + opId + '/image';

     get(url,true).then(function(response) {
         drawFingerFrame(new Uint8Array(response),opId, frameWidth, frameHeight);
    }).catch(function(error) {

    })
}

function drawFingerFrame(frameBytes,opId, frameWidth, frameHeight) {
	fingerFrameNew = document.getElementById("fingerframe");
    var ctx = fingerFrameNew.getContext('2d');
    var imgData= ctx.createImageData(fingerFrame.width,fingerFrame.height);

    for(var i = 0; i < frameBytes.length; i++) {
      // red
      imgData.data[4*i] = frameBytes[i];
      // green
      imgData.data[4*i + 1] = frameBytes[i];
      // blue
      imgData.data[4*i + 2] = frameBytes[i];
      //alpha
      imgData.data[4*i + 3] = 255;
    }

    ctx.putImageData(imgData, 0, 0, 0, 0, fingerFrame.width,fingerFrame.height);
}

function post(url,json) {
  return new Promise(function(resolve, reject) {

    var req = new XMLHttpRequest();
    req.open("POST", url);
    req.setRequestHeader('Content-type', 'application/json; charset=utf-8');

    req.onload = function() {
      if (req.status == 200) {
        resolve(req.response);
      }
      else {
        reject($("#result").html(req.statusText));
      }
    };

    req.onerror = function() {
      reject($("#result").html("Please Configure The Driver!"));
    };

    req.send(json);
  });
}

function getOperationState(opId) {
    var url = fpHTTSrvOpEP + '/' + opId;

     get(url,false).then(function(response) {
         parseOperationDsc(JSON.parse(response));
    }).catch(function(error) {
        $("#result").html("Cancelled")
    })
    
}

function get(url, asArray) {
  return new Promise(function(resolve, reject) {

    var req = new XMLHttpRequest();
    req.open('GET', url);
    
    if(asArray) {
        req.responseType = "arraybuffer";
    }

    req.onload = function() {

      if (req.status == 200) {
        resolve(req.response);
      }
      else {
        reject($("#result").html(req.statusText));
      }
    };

    req.onerror = function() {
      reject($("#result").html("ERROR PLEASE COORDINATE WITH DEVELOPER!"));
    };

    req.send();
  });
}
</script>