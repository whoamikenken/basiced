<?php 
/**
 * @author Kennedy Hipolito
 * @copyright Bente-Bente
 * @copyright Coffee + Memes = Creativity ^_^
 */
$today = date('Y-m-d g:i:s');
?>
<input type="hidden" name="deviceKey" id="deviceKey" value="<?= $deviceKey ?>">
<!-- <a class="btn btn-primary syncLogsImages"><i class="glyphicon glyphicon-refresh"></i>&nbsp;&nbsp;Sync Logs</a><br><br> -->
<div id="personTableDiv">
    
</div>

<script>
var toks = hex_sha512(" ");
var serial_number = $("#deviceKey").val();

loadFacialSetupLoadLogs();

function loadFacialSetupLoadLogs(){
    $("#backTomanage").css("display", "unset");
    code = $(this).attr('code');
    ip = "<?php echo $ip ?>";
    $.ajax({
        type: "POST",
        crossDomain: true,
        url: "http://"+ip+":8090/findRecords",
        data: {
            "pass": "12345678",
                "personId": "-1",
                "length": "-1",
                "index": "0",
                "startTime": "0",
                "endTime": "0",
                "model": "0"
            },
        success:function(response){
            console.log(response);
            $.ajax({
                url: "<?= site_url('facial_/loadFacialLogsTable')?>",
                type: "POST",
                data:{data: response, ip:ip, deviceKey:code},
                success:function(response){
                    $("#personTableDiv").html(response);
                }
            });
        }
    });
}


$(".syncLogsImages").click(function(){
    code = $(this).attr('code');
    ip = "<?php echo $ip ?>";
    deviceKey = "<?php echo $deviceKey ?>";
    var localLogs = [];
    swal.fire({
        html: '<h4>Processing.....</h4>',
        onRender: function() {
            $('.swal2-content').prepend(sweet_loader);
        }
    });

    // var date = new Date();
    // date.setDate(date.getDate() - 3);

    // var from = parseInt((new Date($("#from").val()+ " 01:00 AM").getTime() / 1000).toFixed(0));
    // var to = parseInt((new Date($("#to").val()+ " 11:59 PM").getTime() / 1000).toFixed(0));

    $.ajax({
        type: "POST",
        crossDomain: true,
        dataType: "json",
        url: "http://"+ip+":8090/findRecords",
        data: {
            "pass": "12345678",
                "personId": "-1",
                "length": "-1",
                "index": "0",
                "startTime": "0",
                "endTime": "0",
                "model": "0"
            },
        success:function(response){
            console.log(response.data);
            var record = [];
            for (const element of response.data.records) { // You can use `let` instead of `const` if you like
                
                if(element.state == 0 && element.personId != "STRANGERBABY"){
                    console.log(response.data);
                    // $.ajax({
                    //     url: "http://localhost:8098/api/converterFTP",
                    //     type: "POST",
                    //     contentType: 'application/json',
                    //     data: JSON.stringify({"link": element.path,"base64": "string"}),
                    //     async: false,
                    //     success: function(msg) {
                    //         var obj = {
                    //             'personId': element.personId,
                    //             'time': element.time,
                    //             'deviceKey': deviceKey,
                    //             'type': element.type,
                    //             'base64image': msg.base64,
                    //         }
                            console.log(obj);
                            // $.ajax({
                            //     url: "<?= site_url('facial_/syncLogsLocal')?>",
                            //     type: "POST",
                            //     dataType: 'json',
                            //     async:false,
                            //     data:obj,
                            //     success:function(response){
                            //         console.log(response);
                            //     }
                            // });
                    //     }
                    // });
                    
                }
            }

            
            
        }
    });
});

function getBase64FromImageUrl(url) {
    var img = new Image();

    img.setAttribute('crossOrigin', 'anonymous');

    img.onload = function () {
        var canvas = document.createElement("canvas");
        canvas.width =this.width;
        canvas.height =this.height;

        var ctx = canvas.getContext("2d");
        ctx.drawImage(this, 0, 0);

        var dataURL = canvas.toDataURL("image/png");

        alert(dataURL.replace(/^data:image\/(png|jpg);base64,/, ""));
    };

    img.src = url;
}


function toDataURL(url, callback) {
  var xhr = new XMLHttpRequest();
  xhr.onload = function() {
    var reader = new FileReader();
    reader.onloadend = function() {
      callback(reader.result);
    }
    reader.readAsDataURL(xhr.response);
  };
  xhr.open('GET', url);
  xhr.responseType = 'blob';
  xhr.send();
}

async function file_get_contents(uri, callback) {
    let res = await fetch(uri),
        ret = await res.text(); 
    return callback ? callback(ret) : ret; // a Promise() actually.
}

</script>