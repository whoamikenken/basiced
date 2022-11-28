<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */

?>
<style>
  #content{
    width: calc(100% - 252px);
  }

  .form_row{
    padding-bottom: 10px;
  }

  .panel-body{
    margin-top: 30px;
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
                    <div class="panel-heading" style="background-color: #0072c6;"><h4><b>RFID Registration List</b></h4></div>
                    <div class="panel-body">
                        <div class="form_row" hidden>
                            <label class="field_name align_right">Type</label>
                            <div class="field">
                                    <div class="col-md-6">
                                    <select id="listtype" class="chosen col-md-5">
                                        <option value="E">Employee</option>
                                        <!-- <option value="S">Student</option> -->
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div id="employeelist">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>           

<script>
$(document).ready(function() {
  loadlisttodisplay("","");
})



// $("#departmentid").change(function(){
//     $.ajax({
//         url : "<?=site_url("/setup_/getOffice")?>",
//         type: "POST",
//         data: {department:$(this).val()},
//         success: function(msg){
//             $("#office").html(msg).trigger("chosen:updated");
//         }
//     });
// });

function loadlisttodisplay(dept,yearlevel,section,sy,sem,departmentid,status, empstat, office, teachingtype){
   $("#employeelist").html("Loading ..., this may take a while, please wait..."); 
   var form_data = {
      ltype : $("#listtype").val(),
      dept:dept,
      yearlevel: yearlevel,
      section:section,
      sy:sy,
      sem:sem,
      departmentid:departmentid,
      status:status,
      empstat:empstat,
      office:office,
      teachingtype:teachingtype
   }; 
   $.ajax({
      url : "<?=site_url("process_/cardsetuplist")?>",
      type: "POST",
      data: form_data,
      success: function(msg){
        $("#employeelist").html(msg);
      }
   }); 
}
$(function(){   
$("#listtype").change(function(){
  loadlisttodisplay("","");
});
// loadlisttodisplay("","");
$('.chosen').chosen();     
});
</script>