<?php

/**
 * @author Robert Ram Bolista
 * @copyright ram_bolista@yahoo.com
 * @date 6-27-2014
 * @time 13:16
 */
?>
<div class="widgets_area">
<?php
  // if($job == "delete"){
    ?>
      <!-- <div class="form-group" id="chooseAction">
        <div class="col-md-12">
          <label class="col-md-2 control-label">Action:</label>
          <div class="col-md-5">
            <button class="col-sm-12 btn btn-danger action" value="delete">Delete</button>
          </div>
          <div class="col-md-5">
            <button class="col-md-12 btn btn-success action" value="edit">Edit</button>
          </div>
        </div>
      </div> -->
    <?php
  // }
?>
<input type="hidden" name="updatedJob" id="updatedJob" value="new">
<form id="form_hc" class="form-horizontal" <?=($job == "delete") ? "style='display:none;'" : '' ;?>>
<div class="form-group">
    <label class="col-sm-3 control-label delete_action">Date From</label>
    <div class="col-sm-8">
      <input type="text" class="form-control" name="dfrom" readonly="true" value="<?=date("F d, Y",strtotime($start))?> "/>
    </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label delete_action">To</label>
  <div class="col-sm-8">
    <input type="text" class="form-control" name="dto" readonly="true" value="<?=date("F d, Y",strtotime($end))?> "/>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-3 control-label">Holiday</label>
  <div class="col-sm-8 delete_action">
    <select class="chosen delete_action" name="mh_cal" id="mh_cal">
      <option value="">-Select Holiday-</option>
      <?
        $opt_type = $this->db->query("select DISTINCT holiday_id,hdescription from code_holidays")->result();
        foreach($opt_type as $c){
        ?><option<?=($c->holiday_id==$holiday_c ? " selected" : "")?> desc="<?=Globals::_e($c->hdescription)?>" value="<?=Globals::_e($c->holiday_id)?>"><?=Globals::_e($c->hdescription)?></option><?    
        }
      ?>
    </select>
  </div>
</div>

<div class="form-group">
  <div class="col-sm-1 delete_action">
    <input type="checkbox" class="double-sized-cb" name="halfday" style="margin-left: 150px;">
  </div>
  <label class="col-sm-7" style="margin-left: 130px;">Check this if the holiday is not whole day.</label>
</div>

<div class="form-group hol_time delete_action">
  <div class="col-sm-12">
  <label class="col-sm-3 control-label">Time Start</label>
    <div class="col-sm-4" style="margin-left: -10px;">
        <div class='input-group time'>
            <input type='text' class="form-control"  name="fromtime"/>
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-time"></span>
            </span>
        </div>
    </div>
  <label class="col-sm-1 control-label" style="margin-left: -14px;">End</label>
    <div class="col-sm-4" style="margin-left: -12px;">
        <div class='input-group time delete_action'>
            <input type='text' class="form-control"  name="totime" />
            <span class="input-group-addon">
                <span class="glyphicon glyphicon-time"></span>
            </span>
        </div>
    </div>
  </div>
</div>

<div class="form-group hol_time delete_action">
  <label class="col-sm-3 control-label">Holiday</label>
  <div class="col-sm-8">
    <select name="sched_count" class="chosen">
      <option value="">Select an option</option>
      <option value="first">First Sched</option>
      <option value="second">Second Sched</option>
    </select>
  </div>
</div>
</form>
</div>  
<script>
$(document).ready(function () {
var toks = hex_sha512(" "); 
checkIfHolidayHalfday();
$(".chosen").chosen();
$(".hol_time").hide();

$('.time').datetimepicker({
    format: 'LT'
});
$(".button_save_modal").unbind("click").click(function(){
   $("#updatedJob").val("<?=$job?>");
   if($("#mh_cal").val()==""){
      Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Holiday is required!",
            showConfirmButton: true,
            timer: 1000
        })
      $("#mh_cal").focus();
      return false;
   }

   if((!$("input[name='fromtime']").val() || !$("input[name='fromtime']").val()) && $("input[name='halfday']").is(":checked")){
      Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Time is required!",
            showConfirmButton: true,
            timer: 1000
        })
      return false;
   }
   if(!$("select[name='sched_count']").val() && $("input[name='halfday']").is(":checked")){
      Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: "Schedule is required!",
            showConfirmButton: true,
            timer: 1000
        })
      $("#mh_cal").focus();
      return false;
   }


    var halfday = "";
    if($("input[name='halfday']").is(":checked")) halfday = "on";

    var form_data = {
      dfrom : GibberishAES.enc($("input[name='dfrom']").val(), toks),
      dto : GibberishAES.enc($("input[name='dto']").val(), toks),
      mh_cal : GibberishAES.enc($("select[name='mh_cal']").val(), toks),
      halfday : GibberishAES.enc(halfday, toks),
      fromtime : GibberishAES.enc($("input[name='fromtime']").val(), toks),
      totime : GibberishAES.enc($("input[name='totime']").val(), toks),
      sched_count : GibberishAES.enc($("select[name='sched_count']").val(), toks),
      holiday_c : GibberishAES.enc("<?=$holiday_c?>", toks),
      job : GibberishAES.enc($("#updatedJob").val(), toks),
      hcalendar_id : GibberishAES.enc("<?=$hcalendar_id?>", toks),
      toks:toks
    }
       // console.log(form_data);
   $.ajax({
      url: "<?=site_url("maintenance_/save_holiday_calendar")?>",
      data : form_data,
      type : "POST",
      success:function(msg){
        Swal.fire({
            icon: 'success',
            title: 'Success!',
            text: msg,
            showConfirmButton: true,
            timer: 1000
        })
        $("#hol-view").modal("toggle");
        $('#calendar').fullCalendar( 'refetchEvents' );
      }
   }); 
});

$("input[name='halfday']").change(function(){
    if($(this).prop("checked")) $(".hol_time").show();
    else $(".hol_time").hide();
});

});


function checkIfHolidayHalfday(){
  $.ajax({
    url: "<?= site_url('utils_/checkIfHolidayHalfday') ?>",
    type: "POST",
    data: {holiday_id: GibberishAES.enc("<?=$hcalendar_id?>", toks), toks:toks},
    dataType: "json",
    success:function(response){
      if(response.halfday){
        $("input[name='halfday']").prop("checked", true);
        $("input[name='fromtime']").val(response.fromtime);
        $("input[name='totime']").val(response.totime);
        $("select[name='sched_count']").val(response.sched_count).trigger("chosen:updated");
        $(".hol_time").show();
      }
    }
  });
}

$("#updatedJob").val("<?=$job?>");
// $(".modal-footer").remove('#deletebtn');
// $("#deletebtn").remove();
// $(".modal-footer").find('#modalclose').after('<button class="btn btn-danger action" id="deletebtn" value="delete">Delete</button>');
$(".action").click(function(){
  $("#form_hc").css("display", "unset");
  $("#updatedJob").val($(this).val());
  if($(this).val()=="delete"){
    $(".delete_action").css("pointer-events", "none");
  }
})

$(".action").one('click', function() {
  const swalWithBootstrapButtons = Swal.mixin({
    customClass: {
      confirmButton: 'btn btn-success',
      cancelButton: 'btn btn-danger'
    },
    buttonsStyling: false
  })

  swalWithBootstrapButtons.fire({
    title: 'Are you sure?',
    text: "You won't be able to revert this!",
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'Yes, proceed!',
    cancelButtonText: 'No, cancel!',
    reverseButtons: true
  }).then((result) => {
    if (result.value) {
      
      var form_data = {
      dfrom : GibberishAES.enc($("input[name='dfrom']").val(), toks),
      dto : GibberishAES.enc($("input[name='dto']").val(), toks),
      mh_cal : GibberishAES.enc($("select[name='mh_cal']").val(), toks),
      halfday : GibberishAES.enc($("input[name='halfday']").val(), toks),
      fromtime : GibberishAES.enc($("input[name='fromtime']").val(), toks),
      totime : GibberishAES.enc($("input[name='totime']").val(), toks),
      sched_count : GibberishAES.enc($("select[name='sched_count']").val(), toks),
      holiday_c : GibberishAES.enc("<?=$holiday_c?>", toks),
      job : GibberishAES.enc($("#updatedJob").val(), toks),
      hcalendar_id : GibberishAES.enc("<?=$hcalendar_id?>", toks),
      toks:toks
    }
      $.ajax({
       url:"<?=site_url("maintenance_/save_holiday_calendar")?>",
       data: form_data,
       type: "POST",
       success: function(msg){
        Swal.fire({
          icon: 'success',
          title: 'Success!',
          text: msg,
          showConfirmButton: true,
          timer: 1000
        })
        $("#hol-view").modal("toggle");
        $('#calendar').fullCalendar( 'refetchEvents' );

      }
    });

    } else if (
      result.dismiss === Swal.DismissReason.cancel
      ) {
      swalWithBootstrapButtons.fire(
        'Cancelled',
        'Data is safe.',
        'error'
        )
    }
  })
});
</script>