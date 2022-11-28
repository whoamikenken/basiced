<?php

/**
 * @author Justin
 * @copyright 2016
 */

list($display,$dfrom,$dto) = $this->employeemod->attendance_confirmation(); 
?>
<style>
  #msgbox{padding-left: 1%;padding-right: 1%;}

.chosen-container.chosen-with-drop .chosen-drop {
    width: 100%;
}
.chosen-single{
  width: 100%;
}
</style>
<br>
<div class="form_row no-search">
      <label class="col-md-8">View by cut-off : </label>
</div>
<div class="form_row no-search">
  <div class="col-md-4">
    <select class="chosen" id="cutoff"><?=$this->employeemod->displayCutOff(false)?></select>
  </div>
  <div class="col-md-4" style="margin-left: -15px;">
    <a href="#" class="btn btn-primary" id="search" style="margin-right: 5px;">Search</a>
    <a href="#" class="btn btn-primary" id="print">PRINT</a>
  </div>
</div><br>
<div class="form_row">
    <div id="msgbox">
        <p style="font-size: 14px;" ><?=$display?></p>
    </div>
</div>
<legend id="loadingtag" style="display:none;">Loading ...</legend>
<form id="attForm">
    <input type="hidden" name="datesetfrom">
    <input type="hidden" name="datesetto">
    <input type="hidden" name="fv">
    <input type="hidden" name="edata">
    <input type="hidden" name="view">
</form>
<script>
  var toks = hex_sha512(" ");
$("#viewcutoff").click(function(){  loadattcutoff("<?=$dfrom?>","<?=$dto?>"); $("#confirmbtn").show(); $("#cutoff").val("<?=$dfrom?>,"+"<?=$dto?>");   });
$("#search").click(function()
	{  
	var exp = $("#cutoff").val().split(",");   
	 loadattcutoff(exp[0],exp[1]); 
   remider(); confirmButton();
	 // $("#confirmbtn").hide();   
});

$("#print").click(function(){
    var logdate     = $("#cutoff").val();
        logdate     = logdate.split(",");

    $("#attForm").find("input[name='datesetfrom']").val(logdate[0]);
    $("#attForm").find("input[name='datesetto']").val(logdate[1]);
    $("#attForm").find("input[name='fv']").val("<?=$this->session->userdata('username')?>");
    $("#attForm").find("input[name='edata']").val("NEW");
    $("#attForm").find("input[name='view']").val("process/reports_pdf/individual_attendance");

    $("#attForm").attr("action", "<?=site_url("attendance_/loadAttendanceReport")?>");
    $("#attForm").attr("target", "_blank");
    $("#attForm").attr("method", "post");
    $("#attForm").submit();  
});

$(document).ready(function() {
  remider();
  confirmButton();
  var startdate = $("#startdate").text();
  var enddate = $("#enddate").text();
  if(startdate != "" && enddate != ""){
    $("#loadingtag").show();
    loadattcutoff(startdate,enddate); $("#confirmbtn").hide(); 
    $("#loadingtag").hide();
  }
});

function remider(){
  // $.ajax({
    var cutoff = $('#cutoff').val();
  // });
  $.ajax({
       url      :   "<?php echo site_url("employeemod_/getReminder")?>",
       type     :   "POST",
       data     :   {cutoff : cutoff},
       success  :   function(msg){
         $('#msgbox').html(msg);
       }
    }); 
}

function confirmButton(){
  // $.ajax({
    var cutoff = $('#cutoff').val();
    var cut = cutoff.split(",");
    var dfrom = cut[0];
    var dto = cut[1];
  // });
  $.ajax({
       url      :   "<?php echo site_url("employeemod_/getConfirmButton")?>",
       type     :   "POST",
       data     :   {cutoff : cutoff},
       success  :   function(response){
         if(response=='0'){ $('#confirmbtn').hide(); }
         else{
          var form_data = {
              model :  GibberishAES.enc("checkconfirmed_att", toks),
              dfrom :  GibberishAES.enc(dfrom , toks), 
              dto   :  GibberishAES.enc( dto, toks),
              toks:toks
          }
          $.ajax({
                  url      :   "<?php echo site_url("employeemod_/loadmodelfunc")?>",
                  type     :   "POST",
                  data     :   form_data,
                  success : function(msg){
                    console.log(msg);
                      if(msg == 1){
                         $("#confirmbtn").hide();
                      }
                      else{
                         $('#confirmbtn').show();
                      }    
                  }
              });
         } 
       }
    }); 
}

$(".chosen").chosen();
</script>