<?
$data['title'] = $title;
$data['autoload'] = $autoload;

?>
<html>
 <head> 
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
  <title>Bandi Machine</title>
 </head>
 <body>
    <div id="maincontent" class="maincontent"> 
      <div class="rambo">Press Any Key to Continue</div>
    </div>
 </body> 
</html>
<script type="text/javascript">
var default_display = $("<div class=\"rambo\">Press Any Key to Continue</div>");
$(document).keypress(function(e){
  var ecode = e.KeyCode ? e.KeyCode : e.which;
  if(ecode!=13){
  if($(document).fullScreen(true)){
    $.ajax({
        url: "<?=site_url("inout/dom_");?>",
        type: "POST",
        success: function(msg){
            $("#maincontent").html(msg);
            
        }
    });
  }
  }
});
$(document).ready(function(){
    checkSessionTimeEvent = setInterval("checkphpsession()",1*1000);
});
function checkphpsession(){
  if($("#clock_display").html()!=undefined){
  $.ajax({
     url: "<?=site_url("inout/sessionholder")?>",
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