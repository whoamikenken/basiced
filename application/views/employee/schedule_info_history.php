<?php
/**
 * @author Angelica
 * @copyright 2018
 */
$CI =& get_instance();
$CI->load->model('utils');
$empinfo = $this->session->userdata("personalinfo"); 
$empdetails = $empinfo[0]['employeeid'];
$emptype = $empinfo[0]['emptype'];
$teachingtype = $empinfo[0]['teachingtype'];
$hideAimsDept = $teachingtype == 'teaching' ? '' : ' hidden';

$aimsdept_arr = $CI->utils->getAIMSDepartment();
$subject_arr = $CI->utils->getSubject();
?>

<style>
  a.save-history{
    color: #0D47A1;
    font-weight: bold;
    text-decoration: underline;
  }
  a.save-history:hover{
    color: red;
  }
  .stat-change-deleted{
    color: red;
  }
  .stat-change-updated{
    color: green;
  }

  #message {
    /*background-color: #E0E0E0;*/
    /*-webkit-box-shadow: inset 2px -25px 5px -2px rgba(0,0,0,0.7);
    -moz-box-shadow: inset 2px -25px 5px -2px rgba(0,0,0,0.7);
    box-shadow: inset 2px -25px 5px -2px rgba(0,0,0,0.7);*/

    -webkit-box-shadow: inset -1px -98px 5px -2px rgba(0,0,0,0.6);
    -moz-box-shadow: inset -1px -98px 5px -2px rgba(0,0,0,0.6);
    box-shadow: inset -1px -98px 5px -2px rgba(0,0,0,0.6);

    padding: 5px;
    border-radius: 3px;
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    z-index: 1;
    font-weight: bold;
    color: #FFFFFF;
    font-size: 16px;
  background-color:black ;
  }

  .hidden{
    visibility: hidden;
  }

  input[name='hours'], input[name='breaktime'] {
    text-align: center;
  }

  .swal2-cancel{
    margin-right: 20px;
}

</style>

<div id="message" class="hidden" ></div>

<div class="widgets_area animated fadeIn delay-1s">
<form id="form_schedule">
  <input type="hidden" name="sched_id">
<div class="row">
    <div class="col-md-12">
            <div class="well-content" style="border: transparent !important; margin-top: 20px;">
                <a class="btn btn-danger" id="batchDeleteSchedule" style="float: right; margin-bottom: 1%;">Delete Selected</a>
                  <table class="table table-striped table-bordered table-hover">
                      <thead style="background-color: #0072c6;">
                          <tr>
                              <th rowspan="2" class="align_center">Day of Week</th>
                              <th rowspan="2" class="align_center">Weekly Schedule</th>
                              <th rowspan="2" class="align_center">From</th>
                              <th rowspan="2" class="align_center">To</th>
                              <th colspan="2" class="align_center">First Half</th>
                              <th rowspan="2" class="align_center">Early Dismissal End</th>
                              <!--<th rowspan="" colspan="2" class="align_center">Type</th>-->
                              <th rowspan="2" class="align_center">Flexible</th>
                              <th rowspan="2" class="align_center">Hours</th>
                              <th rowspan="2" class="align_center">Breaktime</th>
                              <!--<th rowspan="2" class="align_center" <?= $teachingtype === "teaching" ? "" : 'style="display: none;"' ?>>Course</th>
                              <th rowspan="2" class="align_center" <?= $teachingtype === "teaching" ? "" : 'style="display: none;"' ?>>Section</th>
                              <th rowspan="2" class="align_center" <?= $teachingtype === "teaching" ? "" : 'style="display: none;"' ?>>Per Subject</th>
                              <th rowspan="2" class="align_center" <?= $teachingtype === "teaching" ? "" : 'style="display: none;"' ?>>AIMS Department</th>-->
                              <th rowspan="2" class="align_center">Effectivity Date</th>
                              <th rowspan="2" class="align_center">Status</th>
                              <th rowspan="2" class="align_center"></th>
                              <th colspan="1" class="align_center" style="border-bottom: 1px solid #0072c6">Select&nbsp;All</th>
                          </tr>
                          <tr>
                              <th>Tardy Start</th>
                              <th>Absent Start</th>
                              <th style="border-top: 1px solid #0072c6; " class="align_center"><input type="checkbox" class="double-sized-cb" id="selectAll" name="selectAll" /></th>
                              <!--<th>LEC</th>
                              <th>LAB</th>-->
                          </tr>
                          <tr>
                           
                          </tr>
                      </thead>
                      <tbody id="schedule">
<?php
                          $weeklySchedules = array("weekly"=>"Weekly","1"=>"1st Week","2"=>"2nd Week","3"=>"3rd Week","4"=>"4th Week","5"=>"5th Week");
                          foreach (array_keys($arr_sched_list) as $dateactive) {
                              $date_effective = new DateTime($dateactive);
                              $date_effective->modify('+1 day');
                              $date_effective = $date_effective->format('Y-m-d');
                              
                              foreach ($scheddays as $idx => $idx_det) {
                                  $newday = true;

                                  if(isset($arr_sched_list[$dateactive][$idx])){
                                      foreach ($arr_sched_list[$dateactive][$idx] as $sched_id => $schedPerDay) {

                                                $dateactive_time = date('H:i:s',strtotime($dateactive));

                                                $sched_start = ($schedPerDay["starttime"] != "" && $schedPerDay["starttime"] != "00:00:00") ? date("h:i A",strtotime($schedPerDay["starttime"])) : "";

                                                $sched_end = ($schedPerDay["endtime"] != "" && $schedPerDay["endtime"] != "00:00:00") ? date("h:i A",strtotime($schedPerDay["endtime"])) : "";

                                                $tardy1 = ($schedPerDay["tardy_start"] != "" && $schedPerDay["tardy_start"] != "00:00:00") ? date("h:i A",strtotime($schedPerDay["tardy_start"])) : "";

                                                $absent1 = ($schedPerDay["absent_start"] != "" && $schedPerDay["absent_start"] != "00:00:00") ? date("h:i A",strtotime($schedPerDay["absent_start"])) : "";

                                                $earlyd = ($schedPerDay["early_dismissal"] != "" && $schedPerDay["early_dismissal"] != "00:00:00") ? date("h:i A",strtotime($schedPerDay["early_dismissal"])) : "";
                                                $weekly_flexible = $schedPerDay["weekly_sched"];
                                                $weekly_sched = "";
                                                if($weekly_flexible != ''){
                                                  foreach (explode(',', $weekly_flexible) as $key => $value) {
                                                      if($weekly_sched != "") $weekly_sched .= "<br>".$weeklySchedules[$value];
                                                      $weekly_sched .= $weeklySchedules[$value];
                                                  }
                                                }else{
                                                  $weekly_sched .= $weeklySchedules['weekly'];
                                                  $weekly_flexible = "weekly";
                                                }
                                                  

                                        ?>
                                          
                                                <tr tag="grp" dayofweek="<?=$idx_det['day_code']?>" sched_id="<?=$sched_id?>"  dateactive_time="<?=$dateactive_time?>">
                                                  <td>
                                                    <?if($newday){
                                                        echo $idx_det['day_name'];
                                                    }?>
                                                  </td>
                                                  <td>
                                                    <div style="text-align: center">
                                                      <a class="btn btn-primary weekly_sched_history" style=" color: white; margin-right: 10px;" href="#weekly-view_history" data-toggle="modal" tag="weekly_sched_history" weekly_flexible = "<?=$weekly_flexible?>"  title="<?=$weekly_sched?>"><i class="glyphicon glyphicon-calendar"></i></a>
                                                    </div>
                                                  </td>
                                                  <td>
                                                    <input type="hidden" name="daycode" value="<?=$idx_det['day_code']?>">
                                                    <div class='input-group time'>
                                                        <input type='text' class="form-control ftime1" name="fromtime" id="ftime" value="<?=$sched_start?>">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                    </div>
                                                  </td>

                                                  <td>
                                                    <div class='input-group time'>
                                                        <input type='text' class="form-control ttime1" name="totime" value="<?=$sched_end?>">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                    </div>
                                                  </td>

                                                  <td>
                                                    <div class='input-group time'>
                                                        <input type='text' class="form-control tardy_f1" name="tardy_f" value="<?=$tardy1?>">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                    </div>
                                                  </td>

                                                  <td>
                                                    <div class='input-group time'>
                                                        <input type='text' class="form-control absent_f1" name="absent_f" value="<?=$absent1?>">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                    </div>
                                                  </td>
                                                  
                                                  <td>
                                                    <div class='input-group time'>
                                                        <input type='text' class="form-control early_d1" name="early_d" value="<?=$earlyd?>">
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-time"></span>
                                                        </span>
                                                    </div>
                                                  </td>
                                                  
                                                   <!--<td class="align_center"><div><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LEC" <?=$schedPerDay['leclab'] == "LEC" ? " checked" : ""?> /></div></td>
                                                   <td class="align_center"><div><input type="checkbox" class="leclab double-sized-cb" name="leclab" value="LAB" <?=$schedPerDay['leclab'] == "LAB" ? " checked" : ""?> /></div></td>-->

                                                  <td class="align_center">
                                                      <input type="checkbox" class="double-sized-cb" name="flexible" <?=$schedPerDay['flexible'] == "YES" ? " checked" : ""?> />
                                                  </td>
                                                  <td class="align_center" style="width: 3.333333%">
                                                      <input class="form-control" type="text" style="width: 100%!important;" name="hours" value="<?=$schedPerDay['flexible']=='YES'?$schedPerDay['hours']:0?>" <?=$schedPerDay['flexible']=='YES'?'':'disabled'?> oninput="return setNumberOnly(this)"> 
                                                  </td>
                                                  <td class="align_center" style="width: 3.333333%">
                                                      <input class="form-control" type="text" style="width: 100%!important;" name="breaktime" value="<?=$schedPerDay['flexible']=='YES'?$schedPerDay['breaktime']:0?>" <?=$schedPerDay['flexible']=='YES'?'':'disabled'?> oninput="return setNumberOnly(this)"> 
                                                  </td>
                                                  <!--<td <?=$hideAimsDept?>>
                                                    <select name="course" id="course" class="course chosen" style="width: 200px;" >
                                                       <?= $this->setup->generateCourseDropdown($schedPerDay["course"]) ?>
                                                    </select>
                                                  </td>
                                                  <td <?=$hideAimsDept?>>
                                                    <select name="section" id="section" class="section chosen" style="width: 200px;" >
                                                       <?= $this->setup->generateSectionDropdown($schedPerDay["course"], $schedPerDay["section"]) ?>
                                                    </select>
                                                  </td>
                                                  <td <?=$hideAimsDept?>>
                                                    <select name="subject" id="subject" class="aimsdept chosen" style="width: 200px;" >
                                                       <?= $this->setup->generateSubjectDropdown($schedPerDay["course"], $schedPerDay["subject"]) ?>
                                                    </select>
                                                  </td>
                                                  <td <?=$hideAimsDept?>>
                                                      <select name="aimsdept" id="aimsdept" class="aimsdept chosen" style="width: 200px;" >
                                                          <option value="" <?=(isset($schedPerDay['aimms']) && $schedPerDay['aimms']=='')?' selected':''?> >Choose Aims department..</option>
                                                          <? foreach ($aimsdept_arr as $key => $desc) { ?>
                                                                <option value="<?=$key?>" <?=(isset($schedPerDay['aimms']) && $schedPerDay['aimms']==$key)?' selected':''?> ><?=$desc?></option>
                                                          <? } ?>
                                                      </select>
                                                    </td>-->
                                                   <td style="width: 12%;">
                                                    <div class='input-group date' id='date_effective' data-date="<?=$date_effective?>" data-date-format="yyyy-mm-dd">
                                                        <input type='text' class="form-control" size="16" name="date_effective" value='<?=$date_effective?>'/>
                                                        <span class="input-group-addon">
                                                            <span class="glyphicon glyphicon-calendar"></span>
                                                        </span>
                                                    </div>
                                                   </td>

                                                   <td class="stat-change">
                                                     
                                                   </td>

                                                   <td style="width: 7.8%;">
                                                    <!-- <div class="btn-group">
                                                      <a href="#" class="save-history btn btn-success" sched_id="<?=$sched_id?>" href="#modal-view" data-toggle="modal">Save</a>
                                                    </div>
                                                    <div class="btn-group">
                                                      <a class='btn btn-danger' href='#' tag='delete_sched_h' sched_id="<?=$sched_id?>" href="#modal-view" data-toggle="modal"><i class='glyphicon glyphicon-trash'></i></a>
                                                    </div> -->
                                                    <a id="<?=$sched_id?>" class="btn btn-success editbtn"  tag="editSched">Save</a>&nbsp;&nbsp;<a id="<?=$sched_id?>" class="btn btn-danger delbtn" tag="deleteSched"><i class="glyphicon glyphicon-trash"></i></a>
                                                   </td>
                                                   <td class="align_center">
                                                     <input type="checkbox" class="double-sized-cb deleteCb" schedid="<?=$sched_id?>" name="deleteCb" />
                                                   </td>

                                                </tr>
                                           
                                      <?

                                              $newday = false;
                                        }
                                  }
                                  
                              }
                          }

?>

                      </tbody>
                  </table>
            </div>
        </div>
    </div>
</form>
</div>    
<div id="delete-alerts" class="hide">
    <div style="text-align: center"><h4>Are you sure you want to <b>delete</b> this schedule?</h4></div>
</div>

<div id="edit-alerts" class="hide">
    <div style="text-align: center"><h4>Are you sure you want to <b>update</b> this schedule?</h4></div>
</div>

<div class="modal fade" id="weekly-view_history" role="dialog" data-backdrop="static">
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
        <center><b><h3 tag="title" class="modal-title" style="font-family: Avenir;">Weekly Schedule</h3></b></center>
      </div>
      <div class="modal-body">
        <div class="row">
              <div tag='weekly-display_history'>
              </div>
          </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success week_save_modal_history"  id='week_save_modal_history'>Save</button>
        <button type="button" class="btn btn-success" data-dismiss="modal" id='week_close_modal_history' style="display: none"></button>
      </div>
    </div>

  </div>
</div> 
<!-- <div class="modal fade" id="confirm_delete_history" data-backdrop="static">
   <div class="modal-dialog">
    <div class="modal-content"> -->

      <!-- Modal Header -->
<!--       <div class="modal-header">
      </div>
  <div class="modal-body">
    <h4>Are you sure you want to <b>delete</b> this schedule?</h4>
  </div>
  <div class="modal-footer">
      <div id="saving">
          <span id="loading_del" hidden=""><img src='<?=base_url()?>images/loading.gif' /> Loading please wait..</span>
          <button type="button" id="confirm_delete_history_btn" class="btn btn-primary">&nbsp;YES&nbsp;</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">&nbsp;NO&nbsp;</button>
      </div>
  </div>
</div>
</div>
</div>
<div class="modal fade" id="confirm_update_history" data-backdrop="static">
  <div class="modal-body">
    <h4>Are you sure you want to <b>update</b> this schedule?</h4>
  </div>
  <div class="modal-footer">
      <div id="saving">
          <span id="loading_update" hidden=""><img src='<?=base_url()?>images/loading.gif' /> Loading please wait..</span>
          <button type="button" id="confirm_update_history_btn" class="btn btn-primary">&nbsp;YES&nbsp;</button>
          <button type="button" class="btn btn-danger" data-dismiss="modal">&nbsp;NO&nbsp;</button>
      </div>
  </div>
</div> -->

<script>
  $(document).ready(function(){
    $("#batchDeleteSchedule").hide().css("pointer-events", "none");
   if ("<?= $teachingtype ?>" == "teaching") {
    $("#sidebar").addClass("active");
    $("#content").addClass("active");
   }else{
    $("#sidebar").removeClass("active");
    $("#content").removeClass("active");
   }
});

$("#selectAll").click(function(){
   if($("#selectAll").prop("checked")){
    $('input[name="deleteCb"]').prop("checked", true);
    $("#batchDeleteSchedule").fadeIn().css("pointer-events", "unset");
   }else{
    $('input[name="deleteCb"]').prop("checked", false);
    $("#batchDeleteSchedule").fadeOut().css("pointer-events", "none");
   }
});

$('input[name="deleteCb"]').click(function(){
  var cbCounter = 0;
    if($(this).prop("checked")){
      $("#batchDeleteSchedule").fadeIn().css("pointer-events", "unset");
    }else{
        $('#selectAll').prop("checked", false);
        $('input[name="deleteCb"]').each(function(){
            if($(this).prop("checked")) cbCounter++;
        })
        if(cbCounter > 0) $("#batchDeleteSchedule").fadeIn().css("pointer-events", "unset");
        else $("#batchDeleteSchedule").fadeOut().css("pointer-events", "none");
    }
})

$("#batchDeleteSchedule").click(function(){
  var cbCounter = 0;
  var schedidbatch = '';
  var parent_del = '';
  var userid = $("input[name=employeeid]").val(); 
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
        $('input[name="deleteCb"]').each(function(){
            if($(this).prop("checked")){
              if(schedidbatch){
                schedidbatch += "~|~"+$(this).attr("schedid");
              }else{
                schedidbatch = $(this).attr("schedid");
              }
              $("tr[sched_id='"+$(this).attr("schedid")+"']").find(".stat-change").removeClass("stat-change-updated").addClass("stat-change-deleted").html('DELETED');
              $("tr[sched_id='"+$(this).attr("schedid")+"']").find("a[tag='deleteSched'], a[tag='editSched']").hide();
              cbCounter++;
            }
        })
        $.ajax({
              url: "<?=site_url("schedule_/batchDeleteSchedule")?>",
              data : {sched_id:schedidbatch, employeeid:userid},
              type : "POST",
              success:function(msg){
                Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: "Schedule History has been deleted successfully.",
                      showConfirmButton: true,
                      timer: 1000
                  })

                setTimeout(function(){
                  $(".active").click();
                }, 1000)
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
})

var parent_del;
var id;

///< delete history ---------------------------------------------------------------------------------------------------
// $(document).on("click","a[tag='delete_sched_h']",function(e){
//     e.preventDefault();
//     parent_del = $(this).closest('tr'); 
//         $("#modal-view").find("h3[tag='title']").text("Delete Batch Scheduling");
//     $('#confirm_delete_history_btn').attr('sched_id',$(this).attr('sched_id'));
//     $('#confirm_delete_history').modal('show');
// });

$(".delbtn").click(function(){
    parent_del = $(this).closest('tr'); 
        id = $(this).attr("id");
        $("input[name='sched_id']").val(id);
        var userid = $("input[name=employeeid]").val(); 
        // var delalert = $('#delete-alerts').clone();
        // delalert.find('#chosen-row').html(id);
        // delalert.find('.del-submit').attr('tagkey',id);
        // delalert.removeClass('hide');
        
        // $("#modal-view").find("h3[tag='title']").text("Delete Schedule History");
        // $("#modal-view").find("#modalclose").text("Yes");
        // $("#modal-view").find("#modalclose").attr("data-dismiss", "")
        // $("#modal-view").find("#button_save_modal").removeClass();
        // $("#modal-view").find("#modalclose").removeClass().addClass("btn btn-danger del-submit");
        // $("#modal-view").find("#button_save_modal").attr("data-dismiss", "modal");
        // $("#modal-view").find("#button_save_modal").text("No");
        // $("#modal-view").find("#button_save_modal").addClass("btn btn-primary");
        // $("#modal-view").find("#button_save_modal").attr("code", id);
        // $("#modal-view").find("div[tag='display']").html( delalert );
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
            $.ajax({
              url: "<?=site_url("schedule_/deleteEmployeeScheduleHistory")?>",
              data : {sched_id:$("input[name='sched_id']").val(), employeeid:userid},
              dataType: 'JSON',
              type : "POST",
              success:function(msg){
                  // $('#delete-alerts').modal('hide');
                  if(msg.err_code == 0){
                   // $('#message').removeClass('hidden').html(msg.msg).css({"color":"yellow","z-index":"3"}).show();
                   //  $('#message').delay(2000).fadeOut();
                    Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: msg.msg,
                      showConfirmButton: true,
                      timer: 1000
                  })
                    
                    parent_del.find(".stat-change").removeClass("stat-change-updated").addClass("stat-change-deleted").html('DELETED');
                    parent_del.find("a[tag='deleteSched'], a[tag='editSched']").hide();
                    $("input[name='sched_id']").val("");
                    $("#modal-view").modal('hide');


                  }else{
                    // $('#message').removeClass('hidden').html(msg.msg).css('color','yellow').show();
                    // $('#message').delay(2000).fadeOut();
                     Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: msg.msg,
                      showConfirmButton: true,
                      timer: 1000
                  })
                  }

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

$(document).on("click", ".del-submit",  function(){
        var userid = $("input[name=employeeid]").val();  
        var id = $(this).attr('tagkey');
        $("#modal-view").find("div[tag='display']").html("<h3>Deleting...</h3>");
        $.ajax({
          url: "<?=site_url("schedule_/deleteEmployeeScheduleHistory")?>",
          data : {sched_id:$("input[name='sched_id']").val(), employeeid:userid},
          dataType: 'JSON',
          type : "POST",
          success:function(msg){
              $('#delete-alerts').modal('hide');
              if(msg.err_code == 0){
               $('#message').removeClass('hidden').html(msg.msg).css({"color":"yellow","z-index":"3"}).show();
                $('#message').delay(2000).fadeOut();
                
                parent_del.find(".stat-change").removeClass("stat-change-updated").addClass("stat-change-deleted").html('DELETED');
                // parent_del.find("a[tag='deleteSched'], a[tag='editSched']").hide();
                $("input[name='sched_id']").val("");
                $("#modal-view").modal('hide');


              }else{
                $('#message').removeClass('hidden').html(msg.msg).css('color','yellow').show();
                $('#message').delay(2000).fadeOut();
              }

          }
     });
    });

$(".editbtn").click(function(){
    parent_del = $(this).closest('tr'); 
        id = $(this).attr("id");
        $("input[name='sched_id']").val(id);
        // var editalert = $('#edit-alerts').clone();
        // editalert.find('#chosen-row').html(id);
        // editalert.find('.edit-submit').attr('tagkey',id);
        // editalert.removeClass('hide');
        // $("#modal-view").find("h3[tag='title']").text("Edit Schedule History");
        // $("#modal-view").find("#modalclose").text("Yes");
        // $("#modal-view").find("#modalclose").attr("data-dismiss", "");
        // $("#modal-view").find("#modalclose").attr("name", "editBtn");
        // $("#modal-view").find("#button_save_modal").removeClass();
        // $("#modal-view").find("#modalclose").removeClass().addClass("btn btn-danger edit-submit");
        // $("#modal-view").find("#button_save_modal").attr("data-dismiss", "modal");
        // $("#modal-view").find("#button_save_modal").text("No");
        // $("#modal-view").find("#button_save_modal").addClass("btn btn-primary");
        // $("#modal-view").find("#button_save_modal").attr("code", id);
        // $("#modal-view").find("div[tag='display']").html( editalert );
        const swalWithBootstrapButtons = Swal.mixin({
          customClass: {
            confirmButton: 'btn btn-success',
            cancelButton: 'btn btn-danger'
          },
          buttonsStyling: false
        })

        swalWithBootstrapButtons.fire({
          title: 'Are you sure you want to update this schedule?',
          text: "You won't be able to revert this!",
          icon: 'warning',
          showCancelButton: true,
          confirmButtonText: 'Yes, proceed!',
          cancelButtonText: 'No, cancel!',
          reverseButtons: true
        }).then((result) => {
          if (result.value) {
           var schedule = "";
    var pars2 = "~u~"; 

    if(parent_del.find("input[name='fromtime']:first").val() && parent_del.find("input[name='totime']:first").val()){
            schedule += schedule ? "|" : ""; 
            schedule += parent_del.attr("dayofweek");
            schedule += pars2;
            schedule += parent_del.find("input[name='fromtime']:first").val()
            schedule += pars2;
            schedule += parent_del.find("input[name='totime']:first").val();
            schedule += pars2;
            schedule += parent_del.find("input[name='tardy_f']:first").val();
            schedule += pars2;
            schedule += parent_del.find("input[name='absent_f']:first").val();
            schedule += pars2;
            schedule += parent_del.find("input[name='early_d']:first").val();
            schedule += pars2;
            schedule += (parent_del.find("input[name='leclab']:checked").val() === undefined ? "" : parent_del.find("input[name='leclab']:checked").val());
            schedule += pars2;
            schedule += (parent_del.find("input[name='flexible']").is(':checked') ? 'YES' : 'NO');
            schedule += pars2;
            schedule += (parent_del.find("input[name='hours']").val());
            schedule += pars2;
            schedule += (parent_del.find("input[name='breaktime']").val());
            schedule += pars2;
            schedule += parent_del.find("input[name='date_effective']:first").val();
            schedule += pars2;
            schedule += parent_del.find("select[name='aimsdept']:first").val();
            schedule += pars2;
            schedule += parent_del.find("select[name='subject']:first").val();
            schedule += pars2;
            schedule += parent_del.find("a[tag='weekly_sched_history']:first").attr("weekly_flexible");
    }

    var form_data = "timesched=" + schedule;
        form_data += "&employeeid="+$("input[name=employeeid]").val();  
        form_data += "&sched_id="+$("input[name='sched_id']").val();  
        form_data += "&dateactive_time="+parent_del.attr('dateactive_time');

        $('#loading_update').show();
        $.ajax({
          url: "<?=site_url("schedule_/updateEmployeeScheduleHistory")?>",
          data : form_data,
          dataType: 'JSON',
          type : "POST",
          success:function(msg){
              // $('#edit-alerts').modal('hide');
              $('#loading_update').hide();

              if(msg.err_code == 0){
                // $('#message').removeClass('hidden').html(msg.msg).css({"color":"yellow","z-index":"3"}).show();
                Swal.fire({
                      icon: 'success',
                      title: 'Success!',
                      text: msg.msg,
                      showConfirmButton: true,
                      timer: 1000
                  })
                // $('#message').delay(2000).fadeOut();
                parent_del.find(".stat-change").removeClass("stat-change-deleted").addClass("stat-change-updated").html('UPDATED');
                $("input[name='sched_id']").val("");
                 $('#button_save_modal').click();
              }else{
                $('#button_save_modal').click();
                // $('#message').removeClass('hidden').html(msg.msg).css('color','red').show();
                // $('#message').delay(2000).fadeOut();
                Swal.fire({
                      icon: 'warning',
                      title: 'Warning!',
                      text: msg.msg,
                      showConfirmButton: true,
                      timer: 1000
                  })
              }

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

$(document).on("click", ".edit-submit",  function(){
  $("#modal-view").find("div[tag='display']").html("<h3>Updating...</h3>");
    var schedule = "";
    var pars2 = "~u~"; 

    if(parent_del.find("input[name='fromtime']:first").val() && parent_del.find("input[name='totime']:first").val()){
            schedule += schedule ? "|" : ""; 
            schedule += parent_del.attr("dayofweek");
            schedule += pars2;
            schedule += parent_del.find("input[name='fromtime']:first").val()
            schedule += pars2;
            schedule += parent_del.find("input[name='totime']:first").val();
            schedule += pars2;
            schedule += parent_del.find("input[name='tardy_f']:first").val();
            schedule += pars2;
            schedule += parent_del.find("input[name='absent_f']:first").val();
            schedule += pars2;
            schedule += parent_del.find("input[name='early_d']:first").val();
            schedule += pars2;
            schedule += (parent_del.find("input[name='leclab']:checked").val() === undefined ? "" : parent_del.find("input[name='leclab']:checked").val());
            schedule += pars2;
            schedule += (parent_del.find("input[name='flexible']").is(':checked') ? 'YES' : 'NO');
            schedule += pars2;
            schedule += (parent_del.find("input[name='hours']").val());
            schedule += pars2;
            schedule += (parent_del.find("input[name='breaktime']").val());
            schedule += pars2;
            schedule += parent_del.find("input[name='date_effective']:first").val();
            schedule += pars2;
            schedule += parent_del.find("select[name='aimsdept']:first").val();
            schedule += pars2;
            schedule += parent_del.find("select[name='subject']:first").val();
            schedule += pars2;
    }

    var form_data = "timesched=" + schedule;
        form_data += "&employeeid="+$("input[name=employeeid]").val();  
        form_data += "&sched_id="+$("input[name='sched_id']").val();  
        form_data += "&dateactive_time="+parent_del.attr('dateactive_time');

        $('#loading_update').show();
        $.ajax({
          url: "<?=site_url("schedule_/updateEmployeeScheduleHistory")?>",
          data : form_data,
          dataType: 'JSON',
          type : "POST",
          success:function(msg){
              $('#edit-alerts').modal('hide');
              $('#loading_update').hide();

              if(msg.err_code == 0){
                $('#message').removeClass('hidden').html(msg.msg).css({"color":"yellow","z-index":"3"}).show();
                $('#message').delay(2000).fadeOut();
                parent_del.find(".stat-change").removeClass("stat-change-deleted").addClass("stat-change-updated").html('UPDATED');
                $("input[name='sched_id']").val("");
                 $('#button_save_modal').click();
              }else{
                $('#button_save_modal').click();
                $('#message').removeClass('hidden').html(msg.msg).css('color','red').show();
                $('#message').delay(2000).fadeOut();

              }

          }
     }); 
});

$(".weekly_sched_history").click(function(){
        var weekly_flexible = $(this).attr("weekly_flexible");
        var week_obj = $(this);
        $.ajax({
          url: "<?=site_url('schedule_/adjust_weekly_schedule_history')?>",
          type: "POST",
          data: {weekly_flexible: GibberishAES.enc(weekly_flexible, toks), toks:toks},
          success:function(response){
            $("div[tag='weekly-display_history']").html(response);

            $("#week_save_modal_history").click(function(){
              if(!$("select[name='wSched_history']").val()){
                Swal.fire({
                    icon: 'warning',
                    title: 'Warning!',
                    text: 'Weekly Schedule is required!',
                    showConfirmButton: true,
                    timer: 1000
                })
              }else{
                $(week_obj).attr("weekly_flexible", $("select[name='wSched_history']").val());
                $("#week_close_modal_history").click();
              }
              
            });
          }
        });
    })

// $(document).on("click","#confirm_delete_history_btn",function(e){
    
//     var form_data = "employeeid="+$("input[name=employeeid]").val();  
//         form_data += "&sched_id="+parent_del.attr('sched_id');  

//     $('#loading_del').show();
//     $.ajax({
//           url: "<?=site_url("schedule_/deleteEmployeeScheduleHistory")?>",
//           data : form_data,
//           dataType: 'JSON',
//           type : "POST",
//           success:function(msg){

//               $('#confirm_delete_history').modal('hide');
//               $('#loading_del').hide();

//               if(msg.err_code == 0){
//                 $('#message').removeClass('hidden').html(msg.msg).css('color','green').show();
//                 $('#message').delay(2000).fadeOut();

//                 parent_del.find(".stat-change").removeClass("stat-change-updated").addClass("stat-change-deleted").html('DELETED');
//                 parent_del.find("a[tag='delete_sched_h'], .save-history").hide();

//               }else{
//                 $('#message').removeClass('hidden').html(msg.msg).css('color','red').show();
//                 $('#message').delay(2000).fadeOut();
//               }

//           }
//      }); 

// });


///< update history ----------------------------------------------------------------------------------------------------
// $(document).on("click",".save-history",function(e){
//     e.preventDefault();
//     parent_del = $(this).closest('tr'); 

//     var error_msg = '';

//     if(parent_del.find("input[name='fromtime']:first").val() && parent_del.find("input[name='totime']:first").val()){
//         // validate here if time from is  greater than to time to. If greater than, log to error_msg
//         // author : justin (with e)
//         tfrom = convertTimeToNumber(parent_del.find("input[name='fromtime']:first").val());
//         tto = convertTimeToNumber(parent_del.find("input[name='totime']:first").val());
//         if(tfrom > tto) error_msg = error_msg + "Please enter a valid time for * " + convertToDay(parent_del.attr("dayofweek")) +"\n";
       
//     }else{
//         error_msg = "Please fill up From time and To time.";
//     }

//     if(error_msg != ""){
//       alert(error_msg);
//       return;
//     }

//     $('#confirm_update_history_btn').attr('sched_id',$(this).attr('sched_id'));
//     $('#confirm_update_history').modal('show');
// });

// $(document).on("click","#confirm_update_history_btn",function(e){

//     var schedule = "";
//     var pars2 = "~u~"; 

//     if(parent_del.find("input[name='fromtime']:first").val() && parent_del.find("input[name='totime']:first").val()){
//         schedule += schedule ? "|" : ""; 
//         schedule += parent_del.attr("dayofweek");
//         schedule += pars2;
//         schedule += parent_del.find("input[name='fromtime']:first").val()
//         schedule += pars2;
//         schedule += parent_del.find("input[name='totime']:first").val();
//         schedule += pars2;
//         schedule += parent_del.find("input[name='tardy_f']:first").val();
//         schedule += pars2;
//         schedule += parent_del.find("input[name='absent_f']:first").val();
//         schedule += pars2;
//         schedule += parent_del.find("input[name='early_d']:first").val();
//         schedule += pars2;
//         schedule += (parent_del.find("input[name='leclab']:checked").val() === undefined ? "" : parent_del.find("input[name='leclab']:checked").val());
//         schedule += pars2;
//         schedule += (parent_del.find("input[name='flexible']").is(':checked') ? 'YES' : 'NO');
//         schedule += pars2;
//         schedule += (parent_del.find("input[name='hours']").val());
//         schedule += pars2;
//         schedule += (parent_del.find("input[name='breaktime']").val());
//         schedule += pars2;
//         schedule += parent_del.find("input[name='date_effective']:first").val();
//     }

//     var form_data = "timesched=" + schedule;
//         form_data += "&employeeid="+$("input[name=employeeid]").val();  
//         form_data += "&sched_id="+parent_del.attr('sched_id');  
//         form_data += "&dateactive_time="+parent_del.attr('dateactive_time');  

//     $('#loading_update').show();
//     $.ajax({
//           url: "<?=site_url("schedule_/updateEmployeeScheduleHistory")?>",
//           data : form_data,
//           dataType: 'JSON',
//           type : "POST",
//           success:function(msg){

//               $('#confirm_update_history').modal('hide');
//               $('#loading_update').hide();

//               if(msg.err_code == 0){
//                 $('#message').removeClass('hidden').html(msg.msg).css('color','green').show();
//                 $('#message').delay(2000).fadeOut();
//                 parent_del.find(".stat-change").removeClass("stat-change-deleted").addClass("stat-change-updated").html('UPDATED');
//               }else{
//                 $('#message').removeClass('hidden').html(msg.msg).css('color','red').show();
//                 $('#message').delay(2000).fadeOut();
//               }

//           }
//      }); 

// });

$(".course").change(function(){
  getAvailableSection($(this).val());
  getAvailableSubject($(this).val());
});

$(".leclab").on('change', function() {
    $(this).closest('tr').find(".leclab").not(this).prop('checked', false);
});

$('input[name=flexible]').on('click',function(){
  if(!$(this).is(':checked')){
    $(this).closest('tr').find('input[name=hours],input[name=breaktime]').val(0).prop('disabled',true);
  }else{
    $(this).closest('tr').find('input[name=hours],input[name=breaktime]').prop('disabled',false);
  }
});

$(".date").datetimepicker({
    format: "YYYY-MM-DD"
});

$(document).ready(function(){
    $(".time").datetimepicker({
        format: "LT"
    });
});

/*$("input[name='fromtime'],input[name='totime'],input[name='tardy_f'],input[name='absent_f'],input[name='tardy_e'],input[name='absent_e'],input[name='early_d']").timepicker({
  minuteStep: 1,
  showSeconds: false,
  showMeridian: true,
  defaultTime: false
});*/

function getAvailableSection(course){
  $.ajax({
    url: "<?= site_url('setup_/getAvailableSection') ?>",
    type: "POST",
    data:{course:course},
    success:function(response){
      $(".section").html(response).trigger("chosen:updated");
    }
  });
}

function getAvailableSubject(course){
  $.ajax({
    url: "<?= site_url('setup_/getAvailableSubject') ?>",
    type: "POST",
    data:{course:course},
    success:function(response){
      $(".subject").html(response).trigger("chosen:updated");
    }
  });
}

// new function for validation in time
// author : Justin (with e)
function convertTimeToNumber(time_val){
  const [time, modifier] = time_val.split(' ');

  let [hours, minutes] = time_val.split(':');

  if (hours === '12') {
    hours = '00';
  }

  if (modifier === 'PM') {
    hours = parseInt(hours, 10) + 12;
  }

  hours = parseInt(hours);
  minutes = parseInt(minutes) / 60;
  return hours + minutes;
}

function convertToDay(index){
  var day = {SUN:'Sunday',M:'Monday',T:'Tuesday',W:'Wednesday',TH:'Thursday',F:'Friday',S:'Saturday'};
  return day[index];
}

 setTimeout(
  function() 
  {
    $(".widgets_area").removeClass("animated fadeIn");
  }, 2000);
    $(".chosen").chosen();
    
</script>
<!-- <script src="<?=base_url()?>js/schedule_management.js"></script> -->