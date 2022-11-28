<?
$data['title'] = $title;
$data['autoload'] = $autoload;
$data['macadd'] = $macadd;

?>
<html>
 <head> 
  <meta name="author" content="Aaron P. Ruanto" />
  <link rel='stylesheet' href="<?=base_url();?>css/inout.css" type="text/css" media="screen" title="no title" charset="utf-8"/>
  <script type="text/javascript" src="<?=base_url();?>js/jQuery-min.js" charset="utf-8"></script>    
  <script type="text/javascript" src="<?=base_url();?>js/fullscreen/jquery.fullscreen-min.js" charset="utf-8"></script>
  <script type="text/javascript">
    
  $(function() {
    
    $(".fullscreen-supported").toggle($(document).fullScreen() != null);
    $(".fullscreen-not-supported").toggle($(document).fullScreen() == null);
    
    $(document).bind("fullscreenchange", function(e) {
       console.log("Full screen changed.");
       $("#status").text($(document).fullScreen() ? 
           "Full screen enabled" : "Full screen disabled");
    });
    
    $(document).bind("fullscreenerror", function(e) {
       console.log("Full screen error.");
       $("#status").text("Browser won't enter full screen mode for some reason.");
    });
    
  });
    
  </script>
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
    <div class="message_todo" id='message_todo'>IN</div>
    <div id="maincontent" class="maincontent"> 
      <div class="rambo">Press * key to Continue</div>
    </div>
 </body> 
</html>
<script type="text/javascript">
var default_display = $("<div class=\"rambo\">Press * key to Continue</div>");
$(document).keypress(function(e){
  var ecode = e.KeyCode ? e.KeyCode : e.which;
  if(ecode==42){  
  /** 
    key : * 
    description : Maximise the page 
   */  
      if($(document).fullScreen(true)){
        $.ajax({
            url: "<?=site_url("inout/dom_");?>",
            type: "POST",
            data:{macadd:"<?=$macadd?>"},
            success: function(msg){
                $("#maincontent").html(msg);
                $("#message_todo").css("display","block");
                $("#barcode").val("");
            }
        });
      }
  }else if(ecode==47){
  /** 
    key : / 
    description : IN 
   */
      if($(document).fullScreen(true)){
         $.when($("#message_todo").html("IN")).done(function(){
            $("#barcode").val("");
         });
      }
  }else if(ecode==45){
  /** 
    key : - 
    description : OUT 
   */
      if($(document).fullScreen(true)){
         $.when($("#message_todo").html("OUT")).done(function(){
            $("#barcode").val("");
         });
      }
  }
});
$(document).ready(function(){
    var enter_seconds = Number(1);
    checkSessionTimeEvent = setInterval("checkphpsession()",enter_seconds*1000);
    //clearAllDisplay = setInterval("clearDisplays()",5*1000); 
});
function checkphpsession(){
  if($("#clock_display").html()!=undefined){
  $.ajax({
     url: "<?=site_url("inout/sessionholder")?>",
     type: "POST",
     data:{macadd:"<?=$macadd?>"},
     success: function(msg){ 
          var timed = $(msg).find("timedisplay");
          var dated = $(msg).find("datedisplay");
          $("#date_display").html(dated);
          $("#clock_display").html(timed);
          clearDisplays();
          //loadlistlog();
     }
  }); 
  }
}
</script>