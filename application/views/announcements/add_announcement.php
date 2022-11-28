<?php
	$datetoday = date("Y-m-d");
	$timetoday = date("h:i A");
	$departments = $this->extras->showdepartment();
	
?>
<style>
input[name=alldept]
{
  /* Double-sized Checkboxes */
  -ms-transform: scale(1.5); /* IE */
  -moz-transform: scale(1.5); /* FF */
  -webkit-transform: scale(1.5); /* Safari and Chrome */
  -o-transform: scale(1.5); /* Opera */
  padding: 10px;
}

.col-sm-12{
  position: unset;
}
.col-sm-6 {
    position: unset;
}
.bootstrap-datetimepicker-widget .datepicker-years thead .picker-switch {
     cursor: default;
     pointer-events: none;
}

.form_row{
  padding-bottom: 10px;
}

.date{
  width: 104.3%;
}

.time{
  width: 104.3%;
}
   .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Announcements</b></h4></div>
                   <div class="panel-body">
                    	<form id="formAnnouncement" autocomplete="off">
                        <input type="hidden" name="ids" value="">
                    		<div class="form_row">
    		                    <label class="field_name align_right col-md-3"><b>Concerned Department</b></label>
    		                    <div class="field">
                                <div class="col-md-9 department_list">
      		                        <select class="form-control chosen" id="departments" name="departments" multiple="">
                                    <option value="alldept">All department</option>
      		                        	<?
      		                        		foreach ($departments as $code => $desc) {?>
      		                        			<option value="<?=$code?>"><?=$desc?></option>
      		                        		<?}
      		                        	?>
      		                        </select>
                                </div>
                            </div>
    		                </div>
                        <!-- div class="form_row">
                          <label class="field_name align_right col-md-3">&nbsp;</label>
                          <div class="field col-md-9" style="margin-left: 34px;">
                            <input type="checkbox" name="alldept" id="terms" value="alldept">&nbsp;&nbsp; <b>All Departments</b>
                          </div>
                        </div> -->
                       <!--  <div class="form_row" style="margin-left: 212px;">
                        </div> -->
                            <div class="form_row">
                                <label class="field_name align_right col-md-3">Date From</label>
                                <div class="field">
                                    <div class="col-md-9" style="padding-left: 0px;">
                                      <div class="col-md-5"  style="padding-right: 0px;">
                                      <div class='input-group date' id="datesetfrom" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" value="<?=$datetoday?>" name="datesetfrom" id="dfrom"/>
                                        <span class="input-group-addon">
                                              <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                      </div>
                                    </div>
                                    <div class="col-sm-2 align_center" style="padding: 0px;" >
                                      <label>To</label>
                                    </div>
                                  <div class="col-md-5" style="padding-left: 0px;">
                                    <div class='input-group date' id="datesetto" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd" >
                                      <input type='text' class="form-control" size="16" value="<?=$datetoday?>" name="datesetto" id="dto"/>
                                      <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                      </span>
                                    </div>
                                  </div>
                                  </div>
                                </div>
                            </div>
                            
                            <div class="form_row"  >
                                <label class="field_name align_right col-md-3">Time Start</label>
                                <div class="field">
                                    <div class="col-md-9"style="padding-left: 0px;">
                                      <div class="col-md-5" style="padding-right: 0px;">
                                      <div class='input-group time'>
                                            <input type='text' class="form-control" name="tfrom" id="tfrom"/>
                                            <span class="input-group-addon">
                                                <span class="glyphicon glyphicon-time"></span>
                                            </span>
                                        </div>
                                    </div>
                                    <div class="col-sm-2 align_center" style="padding: 0px;">
                                    <label>End</label>
                                    </div>
                                  <div class="col-md-5" style="padding-left: 0px;">
                                    <div class='input-group time'>
                                        <input type='text' class="form-control"  name="tto" id="tto"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-time"></span>
                                        </span>
                                    </div>
                                  </div>
                                    </div>
                                </div>
                            </div>
                            
                    		<div class="form_row">
                    		    <label class="field_name align_right col-md-3"><b>Announcements / Events</b></label>
                    		    <div class="field no-search">
                                    <div class="col-md-9">
                                        <textarea rows="4" class="form-control isreq" name="event" id="event" placeholder=""></textarea>
                                    </div>
                    		    </div>
                    		</div>
                    		<div class="form_row"  >
    		                    <label class="field_name align_right col-md-3"><b>Venue</b></label>
    		                    <div class="field no-search">
                                    <div class="col-md-9">
    		                           <input class="form-control isreq" type="text" name="venue" id="venue" value="" />
                                    </div>
    		                    </div>
    		                </div>
    		                <div class="form_row"  >
                    		    <label class="field_name align_right col-md-3"><b>To be posted Until</b></label>
		                        <div class="field">
                                    <div class="col-sm-5" style="padding-right: 0px; width: 30.866667%;">
                                        <div class='input-group date' id="posted_until" data-date="<?=$datetoday?>" data-date-format="yyyy-mm-dd">
                                            <input type='text' class="form-control" value="<?=$datetoday?>" size="16" name="posted_until"/>
                                            <span class="input-group-addon">
                                                  <span class="glyphicon glyphicon-calendar"></span>
                                            </span>
                                        </div>
                                    </div>
		                        </div>
                    		<div class="form_row align_right">
                    			<div id="loading" hidden=""></div>
                    			<div id="saving">
                            <button style="width: 80px;display: none;" type="button" id="cancel_edit" class="btn btn-danger">Cancel</button>
                    				<button style="width: 60px;" type="button" id="save" class="btn btn-success">Save</button>
                    			</div>
                    		</div>
                    		</div>
                    	</form>
                    </div>
                </div>
                <div class="row">  
                    <div class="col-md-12" style="position: unset;">
                        <div id="removeani" class="panel animated fadeIn delay-1s">
                           <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Announcements History</b></h4></div>
                           <div class="panel-body">
                              <div id="a_history"  style="padding-bottom: 31px;"></div>
                             </div>
                        </div>
                    </div>
               </div>
              </div>
            </div>
        </div> 
</div>

<script>
  var toks = hex_sha512(" ");
  if("<?=$this->session->userdata('canwrite')?>" == 0) $(".panel").find("input, select, textarea, #datesetfrom, #datesetto, #posted_until, .btn").css("pointer-events", "none");
  else $(".panel").find("input, select, textarea, #datesetfrom, #datesetto, #posted_until, .btn").css("pointer-events", "");
  $(document).ready(function(){

    $('[name=alldept]').click(function(){
            if($(this).prop("checked") == true){
                $(".department_list").css("pointer-events", "none");
                $("#departments").val("").trigger("chosen:updated");
            }
            else if($(this).prop("checked") == false){
                $(".department_list").css("pointer-events", "");

            }
        });
    });

  $("#departments").change(function(){
    if($(this).val()){
      if(!$(this).val().includes("alldept")){
        $('#departments option[value="alldept"]').attr("disabled", true).trigger("chosen:updated");
        $('#departments option[value!="alldept"]').attr("disabled", false).trigger("chosen:updated");
      }else{
        $('#departments option[value!="alldept"]').attr("disabled", true).trigger("chosen:updated");
        $('#departments option[value="alldept"]').attr("disabled", false).trigger("chosen:updated");
      }
    }else{
      $('#departments option[value="alldept"]').attr("disabled", false).trigger("chosen:updated");
      $('#departments option[value!="alldept"]').attr("disabled", false).trigger("chosen:updated");
    }
  });
 

setTimeout(
  function() 
  {
    $("#removeani").removeClass("animated fadeIn delay-1s");
  }, 1500);

$(document).ready(function(){  
    $(".chosen").chosen();
    $("#datesetfrom,#datesetto,#posted_until").datetimepicker({
        format: "YYYY-MM-DD"
    });
    $('.time').datetimepicker({
        format: 'LT'
    });

    loadAnnouncementHistory();
});

$("#cancel_edit").click(function(){
    const swalWithBootstrapButtons = Swal.mixin({
     customClass: {
       confirmButton: 'btn btn-success',
       cancelButton: 'btn btn-danger'
     },
     buttonsStyling: false
   });

   swalWithBootstrapButtons.fire({
     title: 'Are you sure?',
     text: "Do you really want to cancel?",
     icon: 'warning',
     showCancelButton: true,
     confirmButtonText: 'Yes, proceed!',
     cancelButtonText: 'No, cancel!',
     reverseButtons: true
   }).then((result) => {
     if (result.value) {
        $("#cancel_edit").hide();
        clearEntries();
     } else if (
       result.dismiss === Swal.DismissReason.cancel
     ) {
       swalWithBootstrapButtons.fire(
         'Cancelled',
         'Data is safe.',
         'error'
       )
     }
   });
});

$("#save").unbind("click").click(function(){
	var iscontinue  = true;
    var start   = new Date($("#dfrom").val()),
    	end   = new Date($("#dto").val()),
    	diff  = new Date(end - start),
    	days  = diff/1000/60/60/24;
    if(days < 0) return false;

    $("#formAnnouncement .isreq").each(function(){
	    if($(this).val() == ""){
	        $(this).css("border-color","red").attr("placeholder", "This field is required!.");  
	        iscontinue = false;
	    }
    });

    if(!iscontinue)  return false;
    else{
	    $("#saving").hide();
	    $("#loading").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");

      var formdata   =  '';
        $('#formAnnouncement input, #formAnnouncement select, #formAnnouncement textarea').each(function(){
          if(formdata) formdata += '&'+$(this).attr('name')+'='+$(this).val();
          else formdata = $(this).attr('name')+'='+$(this).val();
       })
        if($("#departments").val().includes("alldept")){
           formdata   += "&alldept=alldept";
        }else{
           formdata   += "&deptids="+$("#departments").val();
        }

	     $.ajax({
         url      :   "<?=site_url("announcements_/saveAnnouncement")?>",
         type     :   "POST",
         data     :   {formdata:GibberishAES.enc( formdata, toks), toks:toks},
         success  :   function(msg){
          msg = msg.replace(/["']/g, "");
          Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: msg,
            showConfirmButton: true,
            timer: 2000
          });
          setTimeout(function(){ location.reload() }, 3000);
         }
      });
	}
    
});

$("input[name='datesetfrom'],input[name='datesetto']").blur(function(){
  var d1 = new Date($("input[name='datesetfrom']").val());
  var d2 = new Date($("input[name='datesetto']").val());
  if(d1 > d2){
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: "Please fill-up a valid date.",
          showConfirmButton: true,
          timer: 2000
      })
      $(this).val("");
      return;
  }
});

$("#event, #venue").on('input',function(){
	$(this).css("border-color","#AAAAAA").attr("placeholder", "");
});
$("input[name='datesetfrom']").change(function(){
	$("#err").remove();
   var  start = new Date($(this).val()),
        end   = new Date($("#dto").val()),
        diff  = new Date(end - start),
        days  = diff/1000/60/60/24;
        if(days < 0)    $("#dayscon").append("<input type='text' style='color: red;border-color:#FFFFFF;' id='err' value='Invalid date range!.'>");
        else            $("#err").remove();
});
$("input[name='datesetto']").change(function(){
	$("#err").remove();
   var  end = new Date($(this).val()),
        start   = new Date($("#dfrom").val()),
        diff  = new Date(end - start),
        days  = diff/1000/60/60/24;
        if(days < 0)    $("#dayscon").append("<input type='text' style='color: red;border-color:#FFFFFF;' id='err' value='Invalid date range!.'>");
        else            $("#err").remove();
});

/*
 *  FUNCTIONS
 */

function loadAnnouncementHistory(){
   $("#a_history").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
      url      :   "<?=site_url("announcements_/loadAnnouncementHistory")?>",
      type     :   "POST",
      // data     :   {stat : stat, cnoti : cnoti},
      success  :   function(msg){
        $("#a_history").html(msg);
        if("<?=$this->session->userdata('canwrite')?>" == 0) $("#a_history").find(".btn").css("pointer-events", "none");
        else $("#a_history").find(".btn").css("pointer-events", "");
      }
   });
}

function clearEntries(){
  $("input[name='ids']").val("");
  $("input[name='datesetfrom']").val("");
  $("input[name='datesetto']").val("");
  $("input[name='tfrom']").val("");
  $("input[name='tto']").val("");
  $("input[name='venue']").val("");
  $("#event").val("");
  $("#departments").val("").trigger("chosen:updated");
  $("input[name='posted_until']").val("");
}

</script>