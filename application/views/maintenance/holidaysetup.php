<?php

/**
 * @date 6-25-2014
 * @time 17:6
 */

?>
<style type="text/css">
      .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
#sidebar ul li.active>a, a[aria-expanded="true"] {
    color: #090909 !important;
    background: #0072c6 !important;
}
</style>
<div id="content" style="width: calc(100% - 268px)!important;" > <!-- Content start -->
   <div class="inner_content" >
   <div class="row" >
   <div class="col-md-12 animated fadeIn delay-1s">
       <div class="well blue">
          <div class="well-content no_padding" >
               <div class="navbar-header" style="margin-bottom: 20px; background-color: #0072c6;">
                   <ul class="nav nav-tabs" id="pinfotab" style="background-color: #0072c6;">
                     <li class="active"><a href="#tab1" data-toggle="tab" >HOLIDAY TYPE</a></li>
                     <li><a href="#tab2" id="tabHoliday" data-toggle="tab">HOLIDAY NAMES</a></li>
                     <li><a href="#tab3" data-toggle="tab">HOLIDAY CALENDAR</a></li>
                     
                   </ul>
               </div>
               <div class="tab-content">
                 <div class="tab-pane active" id="tab1" ld='maintenance/holidaytype'>
                   <!-- HOLIDAY TYPE !-->
                   <?$this->load->view('maintenance/holidaytype');?>
                   <!-- HOLIDAY TYPE -->
                 </div>
                 <div class="tab-pane" id="tab2" ld='maintenance/holiday'>
                   <!-- HOLIDAY NAMES !-->
                   <?
                   // $this->load->view('maintenance/holiday',$data);
                   ?>
                   <!-- HOLIDAY NAMES -->
                 </div>
                 <div class="tab-pane" id="tab3" ld='maintenance/holidaycalendar'>
                   <!-- HOLIDAY CALENDAR !-->
                   <?
                   #$this->load->view('maintenance/holiday_calendar',$data);
                   ?>
                   <!-- HOLIDAY CALENDAR -->
                 </div>
                 
               </div>
           </div>
       </div>
   </div>
   </div>
   </div>
</div>           
<script>
var cancontinue = true;
var toks = hex_sha512(" "); 
var message = "";
$(document).ready(function(){
    $.ajax({
            url: "<?=site_url("employee_/checkhasssession")?>",
            type:"POST",
            success: function(msg){
              
                if($(msg).find("status:eq(0)").text()==0){
                   message = $(msg).find("message:eq(0)").text(); 

                }else{
                   cancontinue = true;
                };
            }
        });
});
function refreshtab(tabn){
    var form_data = { 
      view : GibberishAES.enc($(tabn).attr("ld"), toks),
      toks:toks
    }
    
    $.ajax({
            url: "<?=site_url("main/siteportion")?>",
            data: form_data,
            type:"POST",
            success: function(msg){
                $(tabn).html(msg);
            }
        });
}

$("#pinfotab li").click(function(){
  var obj = $(this).find("a").attr("href");
  // alert(obj); 
  if(!cancontinue) alert(message);
  else{
    refreshtab(obj);
  }  
  return cancontinue;
});
</script>