<?php

/**
 * @author Robert Ram Bolista
 * @copyright ram_bolista@yahoo.com
 * @date 6-27-2014
 * @time 10:20
 */

?> 
<style>
table.table.table-striped.table-bordered.table-hover.dataTable.no-footer.dtr-inline.fixedHeader-locked {
    display: none;
}
</style>
<div class="inner_content">
   <div class="widgets_area">
       <div class="panel" style="margin-top: 37px">
           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Holiday Calendar<a id="holidayc"  href="#hol-view" data-toggle="modal"></a></b></h4></div>
               <div class="panel-body">
               <div id="calendar"></div>
           </div>
       </div>
   </div>
</div>
<div class="modal fade" id="hol-view" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-md">

    <div class="modal-content" >
      <div class="modal-header" >
        <div class="media">
          <div class="media-left">
            <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
          </div>
          <div class="media-body" style=" font-weight: bold;padding-top: 10px; font-family: Avenir;">
            <h4 class="media-heading"  style="font-size: 18px !important"><b>Pinnacle Technologies Inc.</b></h4>
            <p style="font-family:Avenir; margin-top: -1%; font-size: 16px !important; font-weight: 300 !important">D`Great</p>
          </div>
        </div>
        <center><b><h3 tag="title" class="modal-title" style="font-family: 'Poppins';">Modal Header</h3></b></center>
      </div>
      <div class="modal-body">
        <div class="row">
              <div tag='display'>
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" data-dismiss="modal" class="btn btn-danger" id="modalclose">Close</button>
        <button class="btn btn-warning action" id="deletebtn" value="delete">Delete</button>
        <button type="button" class="btn btn-success button_save_modal" id='button_save_modal'>Save</button>
        <div id='leaveloading' style="display: none;"><img class='pull-right' src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..</div>
      </div>
    </div>

  </div>
</div>    
 <script>
 //jQuery.noConflict();
var toks = hex_sha512(" "); 
var calendar;
jQuery(function($) {
  
var date = new Date();
var d = date.getDate();
var m = date.getMonth();
var y = date.getFullYear();

 calendar = $('#calendar').fullCalendar({
	header: {
   left: 'title',
   center: '',
   right: 'today,prev,next' //month,agendaWeek,agendaDay
   },
   buttonText: {
   prev: '<a class="btn btn-info"><i class="glyphicon glyphicon-chevron-left"/></a>',
   next: '<a class="btn btn-info" style="margin-right:20px;"><i class="glyphicon glyphicon-chevron-right"/></a>',
   today: '<a class="btn btn-success">Today</a>'
   },
	selectable: true,
	selectHelper: true,
   droppable: false,
   disableDragging : true,
   eventMouseover: function(calEvent,jsEvent,view){
      $(this).css("cursor","pointer");
   },
	select: function(start, end, allDay) {
	   $("#holidayc").click();
      var start = $.datepicker.formatDate('MM dd, yy', new Date(start));
      var end = $.datepicker.formatDate('MM dd, yy', new Date(end));
      dotoggleuserinfo("Set Holiday",{job:GibberishAES.enc("new", toks),start: GibberishAES.enc(start, toks),end: GibberishAES.enc(end, toks),holiday_c: GibberishAES.enc("", toks),hcalendar_id:GibberishAES.enc("", toks), toks:toks});
	},
	editable: true,
   events: "<?=site_url("maintenance_/holidaycalendarlist")?>",
   eventClick: function(calEvent, jsEvent, view) {
      var start = $.datepicker.formatDate('MM dd, yy', new Date(calEvent.start));
      
      if(calEvent.end==null) var end = $.datepicker.formatDate('MM dd, yy', new Date(calEvent.start));
      else var end = $.datepicker.formatDate('MM dd, yy', new Date(calEvent.end));
      
      $("#holidayc").click();
      dotoggleuserinfodelete("Holiday",{job:GibberishAES.enc("edit", toks),start: GibberishAES.enc(start, toks),end: GibberishAES.enc(end, toks),holiday_c: GibberishAES.enc(calEvent.holiday_id, toks) ,hcalendar_id:GibberishAES.enc(calEvent.id, toks), toks:toks});         
   }
});

function dotoggleuserinfo(title,data){
       $("#hol-view").find("h3[tag='title']").html(title); 
       $("#hol-view").find("div[tag='display']").html("Loading, please wait...");
       $("#button_save_modal").text("Save");   
       $("#deletebtn").hide();
       $.ajax({
           url:"<?=site_url("maintenance_/addnewholidaycalendar")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#hol-view").find("div[tag='display']").html(msg);
           }
       }); 
}

function dotoggleuserinfodelete(title,data){
       $("#hol-view").find("h3[tag='title']").html(title); 
       $("#hol-view").find("div[tag='display']").html("Loading, please wait...");
       // $("#button_save_modal").css("display", "none"); 
       $("#deletebtn").show();
       $.ajax({
           url:"<?=site_url("maintenance_/addnewholidaycalendar")?>",
           data: data,
           type: "POST",
           success: function(msg){
              $("#hol-view").find("div[tag='display']").html(msg);
           }
       }); 
}

if("<?=$this->session->userdata('canwrite')?>" == 0) $("#calendar").css("pointer-events", "none");
else $("#calendar").css("pointer-events", "");
$("#modalclose").on("hidden", function () {
      // table.ajax.reload();
      $('#holiday_name').DataTable().ajax.reload();
    });

});
</script>