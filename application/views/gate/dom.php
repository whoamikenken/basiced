<?php
/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 * Modified : @Justin 2015
 */
  
?>
<style>
.date_display{
    position: absolute; 
    font-size: 40px;
    top: 600px;
    left: 750px;
    width: 500px;
    text-align: right;
    display: block;
    color: #FFFFFF;
}
.clock_display{
    position: absolute; 
    font-size: 90px;
    top: 590px;
    left: 20px;
    display: block;
    color: #FFFFFF;
}  
.dark_back{
    position: absolute;
    left: 0;
    top: 590px;
    background: #fffff;
    width: 100%;
    height: 130px;
}
.instruct{
    position: absolute;
    top: 140px;
    left: 300px;
}
.picture_display{
    margin: auto;
    border: 1px #000 solid;
    background-color: #FFF;
    height: 220px;
    padding: 30px;
    position: absolute;
    display: block;
    top: 140px;
    left: 30px;
    z-index: 11;
    border-radius: 3px;
}
.logo_display{
    margin: auto;
    padding: 10px;
    position: absolute;
    display: block;
    top: 10px;
    left: 10px;
    z-index: 11;
    border-radius: 4px;
}
.personal_info{
    position: absolute; 
    top: 510px;
    left: 30px;
    display: none;
}
.log_list{
    position: absolute; 
    top: 30px;
    left: 630px;
    display: none;
}
.name_title{
    font-family: tahoma;
    font-size: 35px;
} 
.name_display{
    font-family: tahoma;
    font-size: 35px;
    color: red;
}
.message_display{
    position: absolute; 
    top: 300px;
    left: 320px;
    width: 900px;
    font-size: 50px;
    color: blue;
    display: none;
}
.extra_display{
    position: absolute; 
    top: 680px;
    left: 30px;
    font-size: 20px;
    color: red;
    display: block;
}
#barcode{
    position: absolute;
    top: 425px;
    left: 30px;
    width: 262px;
    text-align: center;
    padding: 5px;
    font-size: 45px;
}
.listview{
    border-spacing: 0;
    margin: auto;
    font-size: 14px;
    color: #444;
    background: #fbf8e9;
    -o-transition: all 0.1s ease-in-out;
    -webkit-transition: all 0.1s ease-in-out;
    -moz-transition: all 0.1s ease-in-out;
    -ms-transition: all 0.1s ease-in-out;
    transition: all 0.1s ease-in-out;
    box-shadow:3px 3px 5px #ccc;
    font-family: 'trebuchet MS', 'Lucida sans', tahoma;
}
.listview tr:hover {
    background: #e3dcb6;
    -o-transition: all 0.1s ease-in-out;
    -webkit-transition: all 0.1s ease-in-out;
    -moz-transition: all 0.1s ease-in-out;
    -ms-transition: all 0.1s ease-in-out;
    transition: all 0.1s ease-in-out;     
}
.listview td, .listview th {
    border-left: 1px solid #ccc;
    border-top: 1px solid #ccc;
    padding: 6px;
    text-align: left;    
}

.listview th {
    background-color: #03197A;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#ebf3fc), to(#8AAEED));
    background-image: -webkit-linear-gradient(top, #ebf3fc, #03197A);
    background-image:    -moz-linear-gradient(top, #ebf3fc, #03197A);
    background-image:     -ms-linear-gradient(top, #ebf3fc, #03197A);
    background-image:      -o-linear-gradient(top, #ebf3fc, #03197A);
    background-image:         linear-gradient(top, #ebf3fc, #03197A);
    -webkit-box-shadow: 0 1px 0 rgba(255,255,255,.8) inset; 
    -moz-box-shadow:0 1px 0 rgba(255,255,255,.8) inset;  
    box-shadow: 0 1px 0 rgba(255,255,255,.8) inset;        
    border-top: none;
    text-shadow: 0 1px 0 rgba(255,255,255,.5); 
}

.listview td:first-child, .listview th:first-child {
    border-left: none;
}

.listview th:first-child {
    -moz-border-radius: 6px 0 0 0;
    -webkit-border-radius: 6px 0 0 0;
    border-radius: 6px 0 0 0;
}

.listview th:last-child {
    -moz-border-radius: 0 6px 0 0;
    -webkit-border-radius: 0 6px 0 0;
    border-radius: 0 6px 0 0;
}

.listview th:only-child{
    -moz-border-radius: 6px 6px 0 0;
    -webkit-border-radius: 6px 6px 0 0;
    border-radius: 6px 6px 0 0;
}

.listview tr:last-child td:first-child {
    -moz-border-radius: 0 0 0 6px;
    -webkit-border-radius: 0 0 0 6px;
    border-radius: 0 0 0 6px;
}

.listview tr:last-child td:last-child {
    -moz-border-radius: 0 0 6px 0;
    -webkit-border-radius: 0 0 6px 0;
    border-radius: 0 0 6px 0;
}

</style>

<? 
echo form_open("","id='inout' autocomplete='off'");
echo form_input("barcode","","id='barcode' class='barcode'");
echo form_close();
?>
<div class="dark_back"></div>
<div class="date_display" id='date_display'></div>
<div class="clock_display" id='clock_display'></div>


<div class="picture_display" id='picture_display'><img src='<?=base_url()?>images/heracles.png' width="200px" height="220px"/></div>
<!--<div class="logo_display" id='logo_display'><img src='<?=base_url()?>images/logo_stjude.png' height="80px"/></div>-->
<div class="personal_info" id='personal_info'>
  <table>
    <tr>
      <td class="name_title">Name</td>
      <td class="name_title">:</td>
      <td class="name_display"></td>
    </tr>
  </table>
</div>
<div class="log_list" id="log_list"></div>
<div class="message_display" id='message_display'>IN</div>

<div class="extra_display" id='extra_display'>
<?
/** Get the status of the machine */
$macadd = $this->extras->returnmacaddress();

list($stat,$smessage,$sdescription) = $this->timesheet->machinedisplaystatus($macadd);

if(!$stat) echo $smessage;
else echo "<p style='font-size: 10px;'>TERMINAL : ". strtoupper($sdescription?$sdescription:$macadd)."</p>"; 
# echo $this->extras->getclientipaddress();
?>
</div>
<script>
function loadlistlog(){
    $.ajax({
    url:"<?=site_url("gate/loglist_display")?>",
    type: "POST",
    data: {
        limits : 16,
        macadd:"<?=$macadd?>"
    },
    success: function(msg){  
       if($(msg).find("candisplay:eq(0)").text()){ 
        $("#message_todo").css({"display":"block"});
        $("#log_list").css({"display":"block"});
        $("#log_list").html($(msg).find("display:eq(0)").html());
        //$("#barcode").val("");
       }
    }});
}

$(document).ready(function(){
   
    /**
   //create a new WebSocket object.  */
   //var wsUri = "ws://192.168.2.227:5000/stjudedtr/id_server.php";
   /*      
   var wsUri = "<?=$porturi?>"; 	
   websocket = new WebSocket(wsUri);
   
   //#### Message received from server?
   websocket.onmessage = function(ev) {
		var msg = JSON.parse(ev.data); //PHP sends Json data
		var machineid = msg.machineid; //machineid
		var submachineid = msg.submachineid; //submachineid
		var userid = msg.userid; //userid
        var name = msg.name; //name
		var message = msg.message; //message
        var status = msg.status; //status
        var type = msg.type; //type
        //if(true){
		if(type == 'machine' && (machineid=="<?=$macadd?>" || submachineid=="<?=$macadd?>")){
		    //alert(name);
            $("#message_todo").css({"display":"none"});
            $("#log_list").css({"display":"none"});
            
            if(status!="3" && status!="4" && status!="5" && status!="6" && status!="7"){
              $("#picture_display").html("<img src='<?=site_url("gate/imageview")?>?code="+userid+"' width='200px' height='220px' style='border: transparent;'/>");
              $("#personal_info").css({"display":"block"});
              $("td[class='name_display']").html(name);
            }else{
              $("#picture_display").html("<img src='<?=base_url()?>images/Symbol-Error.png' width='200px'/>");   
            }
            
             $("#message_display").css({"display":"block"});
             $("#message_display").html(message);
             $("#barcode").val("");
            
            var dotCounter = 0;
            (function addDot() {
              setTimeout(function() {
                $("#personal_info").css({"display":"none"});
                $("#message_display").css({"display":"none"});
                $("#picture_display").html("<img src='<?=base_url()?>images/heracles.png' width='200px' height='220px'/>");
                
                if("<?=date("H:i:s")?>">="06:00:00" && "<?=date("H:i:s")?>"<="09:00:00"){
                  $.when($("#message_todo").html("IN")).done(function(){
                        $("#barcode").val("");
                  });    
                }else if("<?=date("H:i:s")?>">="15:00:00" && "<?=date("H:i:s")?>"<="19:00:00"){
                  $.when($("#message_todo").html("OUT")).done(function(){
                        $("#barcode").val("");
                  });   
                }
                
                loadlistlog();
              }, 1000);
            })();  
		}
   };
   */
   $("#inout").submit(function(e){
       if($("#barcode").val()!=""){
        $("#barcode").val( $("#barcode").val().replace("/","").replace("-","") );
        var d = new Date();
        var curr_month  = d.getMonth();
           curr_month = Number(curr_month)<10?"0" + curr_month : curr_month;
        var curr_day  = d.getDate();
           curr_day  = Number(curr_day)<10?"0" + curr_day : curr_day;
        var curr_hour  = d.getHours();
           curr_hour  = Number(curr_hour)<10?"0" + curr_hour : curr_hour;
        var curr_min   = d.getMinutes();
           curr_min   = Number(curr_min)<10?"0" + curr_min : curr_min;
        var curr_sec   = d.getSeconds();
           curr_sec   = Number(curr_sec)<10?"0" + curr_sec : curr_sec;
        
        var localdt = d.getFullYear()+"-"+curr_month+"-"+curr_day+" "+curr_hour+":"+curr_min+":"+curr_sec;
        
        var len = ($("#barcode").val()).length;
        if(len > 4){
            var form_data = {
                job: "log",
        		userid: $("#barcode").val(),
        		macid : "<?=$macadd?>",
                ltype : $("#message_todo").text(),
                localtime : localdt
        		};
        		//convert and send data to server
        		//websocket.send(JSON.stringify(msg));

                $.ajax({
                    url     :"<?=site_url("gate/userlog")?>",
                    type    :   "POST",
                    data    :   form_data,
                    success :   function(msg){
                        var data        = $.parseJSON(msg);
                        var machineid   = data.machineid;
                		var submachineid= data.submachineid; 
                		var userid      = data.userid; 
                        var name        = data.name; 
                		var message     = data.message; 
                        var status      = data.status; 
                        var type        = data.type; 
                        
                        if(type == 'machine' && (machineid=="<?=$macadd?>" || submachineid=="<?=$macadd?>")){
                            $("#message_todo").css({"display":"none"});
                            $("#log_list").css({"display":"none"});
                            
                            /** Display status   */
                            if(status!="3" && status!="4" && status!="5" && status!="6" && status!="7"){
                              $("#picture_display").html("<img src='<?=site_url("gate/imageview")?>?code="+userid+"' width='200px' height='220px' style='border: transparent;'/>");
                              $("#personal_info").css({"display":"block"});
                              $("td[class='name_display']").html(name);
                            }else{
                              $("#picture_display").html("<img src='<?=base_url()?>images/Symbol-Error.png' width='200px'/>");   
                            }
                            
                            /** Display message    */
                             $("#message_display").css({"display":"block"});
                             $("#message_display").html(message);
                             $("#barcode").val("").hide();
                            
                            /** do reset displays   */
                            var dotCounter = 0;
                            (function addDot() {
                              setTimeout(function() {
                                $("#personal_info").css({"display":"none"});
                                $("#message_display").css({"display":"none"});
                                $("#picture_display").html("<img src='<?=base_url()?>images/heracles.png' width='200px' height='220px'/>");
                                
                                if("<?=date("H:i:s")?>">="06:00:00" && "<?=date("H:i:s")?>"<="09:00:00"){
                                  $.when($("#message_todo").html("IN")).done(function(){
                                        $("#barcode").val("");
                                  });    
                                }else if("<?=date("H:i:s")?>">="15:00:00" && "<?=date("H:i:s")?>"<="19:00:00"){
                                  $.when($("#message_todo").html("OUT")).done(function(){
                                        $("#barcode").val("");
                                  });   
                                }
                                loadlistlog();
                                $("#barcode").show().focus();
                              }, 1000);
                            })();  
            		    }
                    }   // end success
                });
                
        }else{
            $("#barcode").val("").hide();
            $("#message_display").css({"display":"block"}).html("Failed. Please tap again..");
            setTimeout(function() {
                $("#barcode").show().focus();
            }, 1000);
        }
       }    
       return false; 
   });
   
   
   $("#barcode").focus(); 
   loadlistlog();
   
}).click(function(){
   $("#barcode").focus(); 
});

$("#barcode").keyup(function(e){
    var c = e.keyCode ? e.keyCode : e.which;
    if(c==13){
        //$("#inout").submit();
    }else if(c==106) $("#barcode").val($("#barcode").val().replace("*",""));
     else if(c==111) $("#barcode").val($("#barcode").val().replace("/",""));
     else if(c==109) $("#barcode").val($("#barcode").val().replace("-",""));
    return false;
});
</script>