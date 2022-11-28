<?php 
/**
 * @author Kennedy Hipolito
 * @copyright 2019
 * Redesigning and upgraded UI
 */
header("strict-transport-security: max-age=31536000");
header("X-Frame-Options: DENY");
header('X-Content-Type-Options: nosniff');
header("Referrer-Policy: no-referrer");
header("Content-Security-Policy-script-src: default-src 'none' style-src 'self' 'unsafe-inline';");

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta http-equiv="content-type" content="text/html; charset=iso-8859-1" />
	<meta name="author" content="Kennedy Hipolito" />
    <!-- <link rel='stylesheet' type='text/css' href='<?=base_url();?>css/jquery.auto-complete.css' /> -->
    <!-- <link rel='stylesheet' type='text/css' href='<?=base_url();?>css/jquery-ui.css' /> -->
    <link rel='stylesheet' href="<?=base_url();?>css/css.css" type="text/css" media="screen" title="no title" charset="utf-8"/>
    <!-- The styles -->
    <link rel="stylesheet" type="text/css" href="<?=base_url()?>css/bstrap/bootstrap.css">
    <link href="<?=base_url()?>css/bstrap/stylesheet.css" rel="stylesheet">
    <link href="<?=base_url()?>icon/font-awesome.css" rel="stylesheet">
    <link href="<?=base_url();?>css/bstrap/style.css" rel="stylesheet">
    <link href="<?=base_url();?>css/extras.css" rel="stylesheet">
    <link href="<?=base_url();?>css/survey.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/af-2.3.3/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-2.0.0/sl-1.3.0/datatables.min.css"/>
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.6.1/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.css">
        
    <!--<link href='http://fonts.googleapis.com/css?family=Open+Sans:400,300,600,700,800' rel='stylesheet' type='text/css'>-->
    <style>
        @font-face {
          font-family: 'Open Sans';
          font-style: normal;
          font-weight: 300;
          src: local('Open Sans Light'), local('OpenSans-Light'), url(<?=base_url()?>woff/DXI1ORHCpsQm3Vp6mXoaTXhCUOGz7vYGh680lGh-uXM.woff) format('woff');
        }
        @font-face {
          font-family: 'Open Sans';
          font-style: normal;
          font-weight: 400;
          src: local('Open Sans'), local('OpenSans'), url(<?=base_url()?>woff/cJZKeOuBrn4kERxqtaUH3T8E0i7KZn-EPnyo3HZu7kw.woff) format('woff');
        }
        @font-face {
          font-family: 'Open Sans';
          font-style: normal;
          font-weight: 600;
          src: local('Open Sans Semibold'), local('OpenSans-Semibold'), url(<?=base_url()?>woff/MTP_ySUJH_bn48VBG8sNSnhCUOGz7vYGh680lGh-uXM.woff) format('woff');
        }
        @font-face {
          font-family: 'Open Sans';
          font-style: normal;
          font-weight: 700;
          src: local('Open Sans Bold'), local('OpenSans-Bold'), url(<?=base_url()?>woff/k3k702ZOKiLJc3WVjuplzHhCUOGz7vYGh680lGh-uXM.woff) format('woff');
        }
        @font-face {
          font-family: 'Open Sans';
          font-style: normal;
          font-weight: 800;
          src: local('Open Sans Extrabold'), local('OpenSans-Extrabold'), url(<?=base_url()?>woff/EInbV5DfGHOiMmvb1Xr-hnhCUOGz7vYGh680lGh-uXM.woff) format('woff');
        }
        /*
         *  Vertical bar Design Added by Justin
         */
        /*::-webkit-scrollbar {
              width: 15px;
        } 
        ::-webkit-scrollbar-track {
              background-color: #b46868;
        }
         
        ::-webkit-scrollbar-thumb {
              background-color: rgba(0, 0, 0, 0.2);
              border-radius: 10px; 
        }
        ::-webkit-scrollbar-button {
              background-color: #7c2929;
        }
         
        ::-webkit-scrollbar-corner {
              background-color: black;           
        }*/
    </style>
    <style>

    .large{
      font-size: 16px;}.notifdiv {color: white;display: inline-block; position: relative; padding: 2px 5px; }.notifcount {position: absolute;top: 8px;left: 13px;background-color: rgba(212, 19, 13, 1);color: #fff;border-radius: 3px;padding: 1px 2px;font: 9px Verdana;
    }

    .sweet_loader {
        width: 140px;
        height: 140px;
        margin: 0 auto;
        animation-duration: 0.5s;
        animation-timing-function: linear;
        animation-iteration-count: infinite;
        animation-name: ro;
        transform-origin: 50% 50%;
        transform: rotate(0) translate(0,0);
      }
      @keyframes ro {
        100% {
          transform: rotate(-360deg) translate(0,0);
        }
      }

    </style>

    <!-- The fav and touch icons -->
    <link rel="apple-touch-icon-precomposed" sizes="144x144" href="<?=base_url()?>css/img/apple-touch-icon-144-precomposed.html">
    <link rel="apple-touch-icon-precomposed" sizes="114x114" href="<?=base_url()?>css/img/apple-touch-icon-114-precomposed.html">
    <link rel="apple-touch-icon-precomposed" sizes="72x72" href="<?=base_url()?>css/img/apple-touch-icon-72-precomposed.html">
    <link rel="apple-touch-icon-precomposed" href="<?=base_url()?>css/img/apple-touch-icon-57-precomposed.html">
    <link rel="shortcut icon" href="<?=base_url()?>css/img/pinnacle.png">
    <?php if(isset($title)){?>
      <title><?=$title?></title>
    <?php }else{ ?>
         <title>Applicant Portal</title>
         <?php } ?>

    <!--
    <script type="text/javascript" src=base_url();?>js/jQuery.js" charset="utf-8"></script>	
    <script type='text/javascript' src=base_url();?>js/jquery-1.9.1.js'></script>	
    <script type='text/javascript' src=base_url();?>js/jquery-ui.js'></script>-->
    
    <script src="<?=base_url()?>jsbstrap/jquery-1.10.2.js"></script>
    <script src="<?=base_url()?>jsbstrap/jquery-ui-1.10.3.js"></script>
    <!-- <script type='text/javascript' src='<?=base_url();?>js/jquery.metadata.js'></script> -->
    <!-- <script type='text/javascript' src='<?=base_url();?>js/jquery.auto-complete.js'></script> -->
    <!-- <script type='text/javascript' src='<?=base_url();?>js/jquery.PrintArea.js'></script> -->
    <script src="<?=base_url()?>jsbstrap/library/chosen.jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chosen/1.4.2/chosen.jquery.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/3.1.2/rollups/md5.js"></script>
    <script src="https://alexcorvi.github.io/heic2any/dist/heic2any.js"></script>
    <script src="<?=base_url()?>js/loadingoverlay.js"></script>
    <script src="<?=base_url()?>js/loadingoverlay.min.js"></script>
    <script src="<?=base_url()?>js/moment.js"></script>
    <!-- <script src="<?=base_url()?>js/survey.js"></script> -->
            
    <script>
        var mainlink = "<?=site_url("main")?>";
    <?
    /** This function check the session every 10 seconds */
    if($this->session->sess_read() && $this->session->userdata("logged_in")){
    ?>      
        $(document).ready(function() {
                var time_out = 900000;
                checkSessionTimeEvent = setInterval("checkphpsession()",time_out);        
        });  
        function checkphpsession(){
          $.ajax({
             url: "<?=site_url("main/sessionchecker")?>",
             type: "POST",
             success: function(msg){ 
                  if($(msg).find("result").text()==1){
                     location.href = "<?=base_url()?>";
                  }
             }
          }); 
        }
    <?}?>    
    </script>
    <script src="<?=base_url()?>jsbstrap/bootstrap.js"></script>
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.collapsible.min.js"></script> -->
<!--     <script src="<?=base_url()?>jsbstrap/library/jquery.mCustomScrollbar.min.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.mousewheel.min.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.uniform.min.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.sparkline.min.js"></script> -->
    
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.easytabs.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/flot/excanvas.min.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/flot/jquery.flot.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/flot/jquery.flot.pie.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/flot/jquery.flot.selection.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/flot/jquery.flot.resize.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/flot/jquery.flot.orderBars.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/maps/jquery.vmap.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/maps/maps/jquery.vmap.world.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/maps/data/jquery.vmap.sampledata.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.autosize-min.js"></script> -->
    <script src="<?=base_url()?>jsbstrap/library/charCount.js"></script>
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.minicolors.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.tagsinput.js"></script> -->
    <script src="<?=base_url()?>jsbstrap/library/jquery.validate.js"></script>
    <script src="<?=base_url()?>jsbstrap/library/fullcalendar.min.js"></script>
    <!-- <script src="<?=base_url()?>jsbstrap/library/footable/footable.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/footable/data-generator.js"></script> -->
    
    <!-- <script src="<?=base_url()?>jsbstrap/library/bootstrap-fileupload.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/bootstrap-select.js"></script> -->
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.bootstrap.wizard.js"></script> -->
    
    <!-- <script src="<?=base_url()?>jsbstrap/library/jquery.inputmask.bundle.js"></script> -->
    
    <!-- <script type="text/javascript" src="<?=base_url();?>js/datatables.min.js"></script> -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/3.7.2/animate.min.css">
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs/jszip-2.5.0/dt-1.10.18/af-2.3.3/b-1.5.6/b-colvis-1.5.6/b-flash-1.5.6/b-html5-1.5.6/b-print-1.5.6/cr-1.5.0/fc-3.2.5/fh-3.1.4/kt-2.5.0/r-2.2.2/rg-1.1.0/rr-1.2.4/sc-2.0.0/sl-1.3.0/datatables.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datetimepicker/4.17.37/js/bootstrap-datetimepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>
    <script src="<?=base_url()?>js/webcam.js"></script>
    <script src="<?=base_url()?>js/gate/sha512.js"></script>
    <script src="<?=base_url()?>js/gibberish-aes-1.0.0.min.js"></script>
</head>
<script type="text/javascript">
  // the loader html
var sweet_loader = '<div class="sweet_loader"><svg viewBox="0 0 140 140" width="140" height="140"><g class="outline"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="rgba(0,0,0,0.1)" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round"></path></g><g class="circle"><path d="m 70 28 a 1 1 0 0 0 0 84 a 1 1 0 0 0 0 -84" stroke="#71BBFF" stroke-width="4" fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-dashoffset="200" stroke-dasharray="300"></path></g></svg></div>';

function encryptForm(form, toks){
    form_data_serialize = form.serialize();
    var myarr = form_data_serialize.split("&");
    var serializeString = "";
    var arrayVal = {};
    var newArray = {};
    var arr = {};
    // Check For Multiple Value
    myarr.forEach((item, index)=>{
        itemArr = item.split("=");
        
        if(itemArr[0] in newArray){

            newArray[itemArr[0]] = newArray[itemArr[0]]+","+itemArr[1];
        }else{
          newArray[itemArr[0]] = itemArr[1];
        }
    })
    // console.log(newArray);

    Object.keys(newArray).forEach(function(key) {
      serializeString += key+"="+encodeURIComponent(GibberishAES.enc(newArray[key], toks))+"&";

    });
    serializeString += "toks="+toks;
    // console.log(serializeString);

    return serializeString;
}

</script>
<input type="hidden" id="site_url" value="<?php echo site_url() ?>">