<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */

?>

<style type="text/css">
  #content{
    width: calc(100% - 252px);
  }

  .form_row{
    padding-bottom: 10px;
  }

  .panel-body{
    margin-top: 30px;
  }


</style>

<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading"><h4><b>Printing List</b></h4></div>
                      <div class="panel-body">
                       <div class="form_row">
                          <label class="field_name align_right" >Type</label>
                          <div class="field">
                            <div class="col-md-5">
                              <select id="listtype" class="form-control">
                                <option value="E">Employee</option>
                                <option value="S">Student</option>
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

function loadlisttodisplay(dept,yearlevel,section,sy,sem,departmentid){
   $("#employeelist").html("Loading ..., this may take a while, please wait..."); 
   var form_data = {
      ltype : $("#listtype").val(),
      dept:dept,
      yearlevel: yearlevel,
      section:section,
      sy:sy,
      sem:sem,
      departmentid:departmentid,
   }; 
   console.log(form_data);
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