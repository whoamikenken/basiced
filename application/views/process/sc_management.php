<?php

/**
 * @author Justin
 * @copyright 2015
 */

$curr_date = date('Y-m-d');
?>
<style type="text/css">
  .form_row{
        padding-bottom: 10px;
    }
</style>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading"><h4><b>Service Credit Management</b></h4></div>
                   <div class="panel-body">
                       <table class="table table-striped table-bordered table-hover" id="positionTable">
                        <div class="modal fade" id="myModal" data-backdrop="static"></div>  
                        <div class="form_row">           
                            <div class="field no-search">
                            <!-- <div class="dark_navigation" style="margin-left: 16px;"><a href="#" data-target="#myModal" tag='addsc' data-toggle="modal" class="glyphicon glyphicon-plus-sign btn btn-primary"> Service Credit Apply</a>
                            <a href="#" data-target="#myModal" tag='add_used' data-toggle="modal" class="glyphicon glyphicon-plus-sign btn btn-primary"> Service Credit Used Apply</a></div> -->
                            </div>
                        </div>                        
                        <br>
                        <div class="form_row">           
                            <label class="field_name align_right">Status</label>             
                            <div class="field no-search">
                                <div class="col-md-6">
                                    <select class="select blue chosen" id="status">
                                    <?
                                        $opt = $this->extras->showCategory();
                                    foreach($opt as $key=>$val){
                                    ?>      
                                            <option value="<?=$key?>"><?=$val?></option><?
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>  
                        </div>
                        <div class="form_row" hidden>
                            <label class="field_name align_right">Department</label>
                            <div class="field no-search">  
                                <select class="chosen col-md-6" name="deptid" id="deptid">
                                      <option value="">All Department</option>
                                    <?
                                      $opt_department = $this->extras->showdepartment();
                                      foreach($opt_department as $c=>$val){
                                      ?><option value="<?=$c?>"><?=$val?></option><?
                                      }
                                    ?>
                                </select>
                            </div>
                        </div>
                         <div class="form_row">
                            <label class="field_name align_right">Date</label>
                            <div class="field">
                                <div class="col-md-12" >
                                  <div class="col-md-3" style="padding-left: 0px;">
                                  <div class='input-group date' id="ldfrom" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="ldfrom" type="text" value="<?=$curr_date?>"/>
                                    <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                  </div>
                                </div>
                              <div class="col-md-3">
                                <div class='input-group date' id="ldto" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                  <input type='text' class="form-control" size="16" name="ldto" type="text" value="<?=$curr_date?>"/>
                                  <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                  </span>
                                </div>
                              </div>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="field">
                                <div id="load" hidden=""></div>
                                <div class="dark_navigation" style="margin-bottom: 10px;">
                                  <a href="#" style="margin-left: 16px;" class="btn btn-primary" id="searchlbtn" style="margin-left: 15px;">Search</a>
                                  <a href="#" data-target="#myModal" tag='addsc' data-toggle="modal" class="btn btn-primary"  style="margin-left: 15px;"> Service Credit Apply</a>
                                  <a href="#" data-target="#myModal" tag='add_used' data-toggle="modal" class="btn btn-primary"  style="margin-left: 15px;"> Service Credit Used Apply</a>
                                </div>
                                <div id="error" hidden=""></div>
                            </div>
                        </div>
                    </div> 
                </div>
                <div id="managesc"></div> 
                <div id="managescu"></div>    
            </div>
        </div>
    </div>    
</div>
<script>
$(document).ready(function()
{
    view_scapply_status();
    view_scapplyuse_status();
})




$("#searchlbtn").unbind('click').click(function(){
    $("#error").hide();
        view_scapply_status();
        view_scapplyuse_status();
});

$("a[tag='addsc']").click(function(){
   $("#modal-view").find("h3[tag='title']").text("Service Credit");
   $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "employeemod", view: "sc_apply_management"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    }); 
   // comment by justin (with e)
   // for ica-hyperion 21194
   /*$("#modal-view").find("h3[tag='title']").text("Add Official Business");
   $("#button_save_modal").text("Save");  
   /*$.ajax({
    url: "<?=site_url('process_/ob_status')?>",
    type: "POST",
    data : {job : "add"},
    success: function(msg){
        $("#modal-view").find("div[tag='display']").html(msg);
    }
   });*/


   // for ica-aims 21194
   // by justin (with e)
   // > new request pero dadaan sa ob_apply.php
});

$("a[tag=add_used]").click(function()
{
     $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {folder: "process", view: "sc_used_management"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });
});

function view_scapply_status(){
   
    $("#managesc").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");    
    $.ajax({
        url: "<?=site_url('process_/view_sc_status')?>",
        type: "POST",
        data: {status : $("#status").val(),dfrom : $("input[name='ldfrom']").val(), dto : $("input[name='ldto']").val()},
        success: function(msg){
           
            $("#managesc").html(msg);
        }
    });
}

function view_scapplyuse_status(){
   
    $("#managescu").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");    
    $.ajax({
        url: "<?=site_url('process_/view_scuse_status')?>",
        type: "POST",
        data: {status : $("#status").val(),dfrom : $("input[name='ldfrom']").val(), dto : $("input[name='ldto']").val()},
        success: function(msg){
           
            $("#managescu").html(msg);
        }
    });
}

$("#dfrom,#ldfrom,#ldto").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();
</script> 
