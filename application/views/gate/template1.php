<?
// $data['title'] = $title;
// $data['autoload'] = $autoload;
?>
<html>
 <head> 
  <link rel='stylesheet' href="<?=base_url();?>css/inout.css" type="text/css" media="screen" title="no title" charset="utf-8"/>
  <script type="text/javascript" src="<?=base_url();?>js/jQuery-min.js" charset="utf-8"></script>    
 <!--  <script type="text/javascript" src="<?=base_url();?>js/fullscreen/jquery.fullscreen-min.js" charset="utf-8"></script> -->
  <script type="text/javascript">
    
  /*$(function() {
    $(document).fullScreen(true);
    $(".fullscreen-supported").toggle($(document).fullScreen() != null);
    $(".fullscreen-not-supported").toggle($(document).fullScreen() == null);
    
    $(document).bind("fullscreenchange", function(e) {
       $("#status").text($(document).fullScreen() ? 
           "Full screen enabled" : "Full screen disabled");
    });
    
    $(document).bind("fullscreenerror", function(e) {
       console.log("Full screen error.");
       $("#status").text("Browser won't enter full screen mode for some reason.");
    });

  
    
  });
*/


  </script>
  <!-- The fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=base_url()?>css/img/apple-touch-icon-144-precomposed.html">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?=base_url()?>css/img/apple-touch-icon-114-precomposed.html">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?=base_url()?>css/img/apple-touch-icon-72-precomposed.html">
    <link rel="apple-touch-icon-precomposed" href="<?=base_url()?>css/img/apple-touch-icon-57-precomposed.html">
    <link rel="shortcut icon" href="<?=base_url()?>css/img/pinnacle.png">
  <title>Bundy Machine</title>
  <style>
    .message_todo{
        position: absolute; 
        top: 250px;
        left: 300px;
        width: 320px;
        font-size: 100px;
        color: blue;
        text-align: center;
        display: none;
    }
  </style>
 </head>
 <body>
    <!-- <div class="message_todo" id='message_todo'>IN</div> -->
    <div id="maincontent" class="maincontent"> 
      <div class="rambo">Press * key to Continue</div>
    </div>
 </body> 
</html>
<script type="text/javascript">
var default_display = $("<div class=\"rambo\">Press * key to Continue</div>");
function loadpage(){
$.ajax({
  url: "<?=site_url("gate1/dom_");?>",
  type: "POST",
  success: function(msg){
  $("#maincontent").html(msg);
  $("#message_todo").css("display","block");
  $("#barcode").val("");
  }
});
}
loadpage();

var c_stat = "<?=date('H')?>";
var c_hour = "<?=date('H')?>";
var c_min = "<?=date('i')?>";
var c_sec = "<?=date('s')?>";
var curr_stat   = "AM";
var prefresh = 0;

function add_zero(val){
    var str = ""+val;
    if(str.length == 1){
        return "0"+val;
    }else{
        return val;
    }
}

$(document).ready(function(){
    
    var enter_seconds = Number(1);
   checkSessionTimeEvent = setInterval("change_time()",enter_seconds*1000);

});

function timedMsg(){
   var t=setInterval("change_time()",1000);
}

function change_time(){
   //var d = new Date("<?=date("F d, Y H:i:s")?>");
   var e = new Date();
   //var d = new Date();
   var weekday = new Array(7);
       weekday[0]=  "Sunday";
       weekday[1] = "Monday";
       weekday[2] = "Tuesday";
       weekday[3] = "Wednesday";
       weekday[4] = "Thursday";
       weekday[5] = "Friday";
       weekday[6] = "Saturday";
   var month = new Array();
       month[0] = "January";
       month[1] = "February";
       month[2] = "March";
       month[3] = "April";
       month[4] = "May";
       month[5] = "June";
       month[6] = "July";
       month[7] = "August";
       month[8] = "September";
       month[9] = "October";
       month[10] = "November";
       month[11] = "December";    
   
   var curr_date  = e.getDate();
   var curr_day   = e.getDay();
   var curr_month = e.getMonth();
   var curr_year  = e.getFullYear();
   
   if(c_stat < 12)  curr_stat = "AM";
   else             curr_stat = "PM";
   
   if(c_hour > 12){
       c_hour = c_hour - 12;
   }
   if(c_stat == 24)    c_stat = 0;
   
   
    // $("#clock_display").html(curr_hour+":"+curr_min+":"+curr_sec+" "+curr_stat);
   $("#clock_display").html(add_zero(c_hour)+":"+add_zero(c_min)+":"+add_zero(c_sec)+" "+curr_stat);
    $("#date_display").html(month[curr_month]+" "+curr_date+", "+curr_year+"<br>"+weekday[curr_day]);
    
    c_sec++;
    if(c_sec == 60){
        c_min++;
        prefresh++;
        c_sec = 0;
    }
    if(c_min == 60){
        c_hour++;
        c_stat++;
        c_min = 0;
        c_sec = 0;
    }
    if(prefresh == 18) location.reload();
}

function checkphpsession(){
  if($("#clock_display").html()!=undefined){
  $.ajax({
     url: "<?=site_url("gate/sessionholder")?>",
     type: "POST",
     success: function(msg){ 
          var timed = $(msg).find("timedisplay");
          var dated = $(msg).find("datedisplay");
          $("#date_display").html(dated);
          $("#clock_display").html(timed);
     }
  }); 
  }
}
</script>