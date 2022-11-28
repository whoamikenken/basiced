<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
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
    background: #0E2D00;
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
    left: 30px;
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
    /** 
    border: transparent;
    top: 250px;
    left: 650px; 
     */
    /** left: 0; */
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
    background-color: #BCD2AD;
    background-image: -webkit-gradient(linear, left top, left bottom, from(#ebf3fc), to(#BCD2AD));
    background-image: -webkit-linear-gradient(top, #ebf3fc, #BCD2AD);
    background-image:    -moz-linear-gradient(top, #ebf3fc, #BCD2AD);
    background-image:     -ms-linear-gradient(top, #ebf3fc, #BCD2AD);
    background-image:      -o-linear-gradient(top, #ebf3fc, #BCD2AD);
    background-image:         linear-gradient(top, #ebf3fc, #BCD2AD);
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

<div class="instruct">
  <table>
    <tr>
      <td style="font-size: 18px;color: red;">Instruction:</td>
    </tr>
    <tr>
      <td style="font-size: 18px;padding: 0 0 0 20px;">Press "/" to time IN</td>
    </tr>
    <tr>
      <td style="font-size: 18px;padding: 0 0 0 20px;">Press "-" to time OUT</td>
    </tr>
  </table>
</div>

<div class="picture_display" id='picture_display'><img src='<?=base_url()?>images/heracles.png' width="200px" height="220px"/></div>
<div class="logo_display" id='logo_display'><img src='<?=base_url()?>images/logo_stjude.png' height="100px"/></div>
<div class="personal_info" id='personal_info'>
  <table>
    <tr>
      <td class="name_title">Name</td>
      <td class="name_title">:</td>
      <td class="name_display">Robert Ram Bolista</td>
    </tr>
  </table>
</div>
<div class="log_list" id="log_list"></div>
<div class="message_display" id='message_display'>IN</div>

<div class="extra_display" id='extra_display'>
<?
/** Get the status of the machine */
$macadd = $macadd?$macadd:$this->extras->returnmacaddress();
# list($stat,$smessage,$sdescription) = $this->timesheet->machinedisplaystatus($macadd);
# echo "<p style='font-size: 10px;'>TERMINAL : ". strtoupper($sdescription?$sdescription:$macadd)."</p>";
# echo $smessage;
# echo $this->extras->getclientipaddress();
# echo GetHostByName($this->extras->getclientipaddress());
?>
</div>
<script>
var hasdisplay = false;
function loadlistlog(){
    $.ajax({
            url:"<?=site_url("inout/loglist_display")?>",
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
                $("#barcode").val("");
               }
            }});
}
$("#inout").submit(function(e){
   if($("#barcode").val()!=""){
    $("#barcode").val( $("#barcode").val().replace("/","").replace("-","") );
   $.ajax({
            url:"<?=site_url("inout/logme")?>",
            type: "POST",
            data: {
                uid : $("#barcode").val(),
                macadd:"<?=$macadd?>",
                ltype: $("#message_todo").text()
            },
            success: function(msg){
             if($("#barcode").val()!="" && !hasdisplay){
                clearDisplays();
              }
            }
        });     
   }    
   return false; 
});
$(document).ready(function(){
   $("#barcode").focus(); 
   loadlistlog();
}).click(function(){
   $("#barcode").focus(); 
});
$("#barcode").keyup(function(e){
    var c = e.keyCode ? e.keyCode : e.which;
    if(c==13){
        //$("#inout").submit();
    }else if(c==106) $("#barcode").val($("#barcode").replace("*",""));
     else if(c==111) $("#barcode").val($("#barcode").val().replace("/",""));
     else if(c==109) $("#barcode").val($("#barcode").val().replace("-",""));
    return false;
});
function dodisplaystaffinfo(msg){
    $("#message_todo").css({"display":"none"});
    $("#log_list").css({"display":"none"});
    
    /** Display status */
    if($(msg).find("status:eq(0)").text()!="3" && $(msg).find("status:eq(0)").text()!="4" && $(msg).find("status:eq(0)").text()!="5" && $(msg).find("status:eq(0)").text()!="6" && $(msg).find("status:eq(0)").text()!="7"){
      $("#picture_display").html("<img src='<?=site_url("inout/imageview")?>?code="+$(msg).find("userid:eq(0)").text()+"' width='200px' height='220px' style='border: transparent;'/>");
      $("#personal_info").css({"display":"block"});
      $("td[class='name_display']").html($(msg).find("fullname:eq(0)").text());
    }else{
      $("#picture_display").html("<img src='<?=base_url()?>images/Symbol-Error.png' width='200px'/>");   
    }
    
    /** Display message */ 
     $("#message_display").css({"display":"block"});
     $("#message_display").html($(msg).find("message:eq(0)").text());
     $("#barcode").val("");
     
    /** do reset displays */
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
        hasdisplay = false;
      }, 1000);
    })();
}
function clearDisplays(){
    if(!hasdisplay){
    $.ajax({
        url:"<?=site_url("inout/triggerdisplay")?>",
        type: "POST",
        data:{macadd:"<?=$macadd?>"},        
        success: function(msg){
        // $(msg).find("fullname:eq(0)").text()!="" &&
         if(!hasdisplay && $(msg).find("message:eq(0)").text()!=""){
            hasdisplay = true;
            dodisplaystaffinfo(msg);
          }
        }
    });  
    }
}
</script>