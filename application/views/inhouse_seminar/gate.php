<?php    
/**
 * @author KENNNEDY 
 * @copyright Bente-Bente 
 */     

?> 
<link rel='stylesheet' type='text/css' href='<?=base_url();?>css/gate.css' />
<!-- <div><h2 style="
    text-align: center;
    background-color: black;
    margin: 0;
    padding-top: 0.7%;
    padding-bottom: 0.7%;
    border: 1px solid white;
    color: white;
    border-bottom-left-radius: 11px;
    border-bottom-right-radius: 11px;
">AMS(ATTENDANCE MANAGEMENT SYSTEM)</h2></div> -->
<input type="hidden" id="site_url" value="<?= site_url() ?>">
<div id="contentNew" class="no-sidebar" style="margin-left: 0px;">  
<div class="inner_content">   
<input type="hidden" id="servertime">
<form id="form-gate" enctype="multipart/form-data" >
<input type="hidden" name="match" id="match" value="" > 
<input type="hidden" name="id" id="id" value="">
<input type="hidden" name="Fullname" id="Fullname" value="" > 
<input type="hidden" name="ChkIn" id="ChkIn" value="">
<input type="hidden" name="personRegistered" id="personRegistered" value="">  
<input type="hidden" name="base_url" id="base_url" value="<?= base_url(); ?>">
<br>
<div class="login-gate" >  
    <table style="width: 100%;" border="0" cellspacing="15" cellpadding="15"  >
        <tr>
            <td style="width:25%; vertical-align: top;" rowspan="5" >
                <table style="width: 100%;" border="0" >
                    <tr>
                        <td class="text-center" >
                            <img src="<?php echo base_url(); ?>images/school_logo.png" alt="" width='100%' height="285" id="img-profile" align="middle" />
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td class="text-center" >
                            <input type="password" class="text-center" style="width: 100%; height:60px; font-size: 25px;" name="login-rfid" id="login-rfid" value="" placeholder="" autocomplete="false" >
                        </td>
                    </tr>
                    <tr style="display: none;" >
                        <td class="text-center" ><img src="<?php echo base_url(); ?>images/no-image.jpg" alt="" width="256" height="288" id="imgDiv" align="middle" /></td>
                    </tr>
                    <tr style="display: none;" >
                        <td><input type="password" style="width: 100%;" class="text-center" name="login-password" id="login-password" value="" ></td>
                    </tr>  
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td>
                            <span id="container-alert" ></span>
                            <span id="terminal-name" style="font-size: 15px;font-weight: bold;letter-spacing: 2px;">Terminal : </span>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <canvas id="fingerframe" height="480" width="320" style="display: none"></canvas>
                        </td>
                    </tr>
                </table>
            </td>
            <td style="vertical-align: top;" >  
                <table style="width: 100%;" border="0" >
                    <tr>
                        <td class="new-login opacity" >
                            <div class="clock" >
                                <div id="Date"><?php echo date("D d F Y", strtotime($this->db->query("SELECT CURRENT_TIMESTAMP")->row()->CURRENT_TIMESTAMP)); ?></div>
                                <ul class="timer">
                                    <li id="hours">--</li>
                                    <li id="point">:</li>
                                    <li id="min">--</li>
                                    <li id="point">:</li>
                                    <li id="sec">--</li>
                                    <li>&nbsp;</li>
                                    <li id="periods">--</li>
                                </ul> 
                            </div> 
                        </td>
                    </tr>
                    <tr><td>&nbsp;</td></tr>
                    <tr>
                        <td class="new-login opacity" style="vertical-align: middle; text-align: center; height: 90px;" >
                            <div style="font-size: 45px;color: black;" class="text-default" id="container-response" ><strong>Good day, please sign in</strong></div>
                        </td>
                    </tr>
                    <tr>
                        <td>
                        <div style="margin-top: 20px;background-color: black;text-align: center;color: white;" id="resultDiv">
                        </div> 
                            <table id="tblrecords" style="width: 100%;" cellpadding="6" class="table table-bordered table-hover datatable" >
                                <thead>
                                    <tr style="color: white"> 
                                        <th>Date</th>
                                        <th>Time</th>
                                        <th>Employee ID</th>
                                        <th>Type</th>
                                        <th>User</th> 
                                    </tr>
                                </thead>
                                <tbody style="color: white">
                                </tbody>
                            </table>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</div> 
</form>

</div>
</div>
<audio controls id="failed_sound" style="visibility: hidden;">
    <source src="<?=base_url()?>images/buzzer.mp3" type="audio/mpeg" preload="none">
</audio>

<audio controls id="success_sound" style="visibility: hidden;">
    <source src="<?=base_url()?>images/success.mp3" type="audio/mpeg" preload="none">
</audio>
<button onclick="play_failed('play')" id="play_failed" style="visibility: hidden;">PLAY</button>
<button onclick="play_success('play')" id="play_success" style="visibility: hidden;">PLAY</button>
<script>

    var server_unixtime = "";

    $(document).ready(function(){

      $.ajax({
          url:"<?=site_url("fingerprint_/getServerTime")?>",
          type:"POST",
              success:function(msg)
              {
                  server_unixtime = msg;
              }
          });
      });

    function play_failed(task) {
        $("#failed_sound").trigger('load');
        
          if(task == 'play'){
               $("#failed_sound").trigger('play');
          }
          if(task == 'stop'){
               $("#failed_sound").trigger('pause');
               $("#failed_sound").prop("currentTime",0);
          }
     }

    function play_success(task) {
        $("#success_sound").trigger('load');
        
          if(task == 'play'){
               $("#success_sound").trigger('play');
          }
          if(task == 'stop'){
               $("#success_sound").trigger('pause');
               $("#success_sound").prop("currentTime",0);
          }
     }

     function getLocalIP() {
        return new Promise(function(resolve, reject) {
        // NOTE: window.RTCPeerConnection is "not a constructor" in FF22/23
        var RTCPeerConnection = /*window.RTCPeerConnection ||*/ window.webkitRTCPeerConnection || window.mozRTCPeerConnection;

        if (!RTCPeerConnection) {
          reject('Your browser does not support this API');
        }
        
        var rtc = new RTCPeerConnection({iceServers:[]});
        var addrs = {};
        addrs["0.0.0.0"] = false;
        
        function grepSDP(sdp) {
            var hosts = [];
            var finalIP = '';
            sdp.split('\r\n').forEach(function (line) { // c.f. http://tools.ietf.org/html/rfc4566#page-39
                if (~line.indexOf("a=candidate")) {     // http://tools.ietf.org/html/rfc4566#section-5.13
                    var parts = line.split(' '),        // http://tools.ietf.org/html/rfc5245#section-15.1
                        addr = parts[4],
                        type = parts[7];
                    if (type === 'host') {
                        finalIP = addr;
                    }
                } else if (~line.indexOf("c=")) {       // http://tools.ietf.org/html/rfc4566#section-5.7
                    var parts = line.split(' '),
                        addr = parts[2];
                    finalIP = addr;
                }
            });
            return finalIP;
        }
        
        if (1 || window.mozRTCPeerConnection) {      // FF [and now Chrome!] needs a channel/stream to proceed
            rtc.createDataChannel('', {reliable:false});
        };
        
        rtc.onicecandidate = function (evt) {
            // convert the candidate to SDP so we can run it through our general parser
            // see https://twitter.com/lancestout/status/525796175425720320 for details
            if (evt.candidate) {
              var addr = grepSDP("a="+evt.candidate.candidate);
              resolve(addr);
            }
        };
        rtc.createOffer(function (offerDesc) {
            rtc.setLocalDescription(offerDesc);
        }, function (e) { console.warn("offer failed", e); });
      });
    }

</script>
