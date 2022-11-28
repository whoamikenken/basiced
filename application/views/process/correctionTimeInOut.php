<?php

/**
 * @author Justin (with e)
 * @copyright 2017
 *
 * > for ica-hyperion 21194 & 21196
 */

$curr_date = date('Y-m-d');
?>

<style>
    #process_adj{
        font-size: 15px;
        color: #6A1B9A;
        font-weight: bold;
        font-style: italic;
        text-decoration: underline;
        cursor: pointer;
    }
    #process_adj:hover{
        color : #1E88E5;
        font-style: normal;
    }
    #wrap_adj{
        margin-top: 10px;
        margin-left: 0px;
    }

    .form_row{
        padding-bottom: 10px;
    }
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Correction For Time In/Out Management</b></h4></div>
                   <div class="panel-body">
                    <br>
                        <div class="modal fade" id="myModal" data-backdrop="static"></div>        
                        <div class="form_row">           
                            <div class="field no-search">
                            <!-- modified by justin (with e) for ica-hyperion 21194 -->
                            </div>
                        </div>            
                        <div class="form_row">           
                            <label class="field_name align_right">Status</label>
                            <div class="field no-search">          
                              <div class="col-md-6">  
                                <select class="select blue chosen" id="category">
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
                          <span class="pull-right" id="wrap_adj"><a href="#" data-toggle="modal" data-target="#myModal" id="process_adj" style="color:black">Process Adjustment</a></span>
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
                                <div class="col-md-12" style="padding-left: 0px;">
                                  <div class="col-md-3">
                                  <div class='input-group date' id="ldfrom" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control" size="16" name="ldfrom" type="text" value="<?=$curr_date?>"/>
                                    <span class="input-group-addon">
                                          <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                  </div>
                                </div>
                              <div class="col-md-3" style="margin-left: 8px;">
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
                                  <div class="dark_navigation">
                                    <a href="#" class="btn btn-primary" style="margin-left: 15px;" id="searchlbtn" style="margin-left: 15px;">Search</a>
                                    <a href="#" data-target="#myModal" tag='add_d' data-toggle="modal" class="btn btn-primary" style="margin-left: 15px;"> Apply Correction for Time In/Out</a>       
                                </div>
                              
                            </div>
                        </div>
                        <br>  
                    </div>
                </div>    
              <div id="manageleave"></div> 
            </div>
        </div>
    </div>    
</div>
<script>
var toks = hex_sha512(" ");
if("<?=$this->session->userdata('canwrite')?>" == 0) $("a[tag='add_d'], #process_adj").css("pointer-events", "none");
else $("a[tag='add_d'], #process_adj").css("pointer-events", "");
$("#searchlbtn").click(function(){
    view_leave_status();
});

$("a[tag='add_d']").click(function(){
   
   // comment by justin (with e)
   // for ica-hyperion 21194
   /*$("#modal-view").find("h3[tag='title']").text("Add Official Business");
   $("#button_save_modal").text("Save"); */ 
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
   $.ajax({
        url      : "<?=site_url("employeemod_/fileconfig")?>",
        type     : "POST",
        data     : {toks:toks,folder: GibberishAES.enc("employeemod", toks), view: GibberishAES.enc("correction/correction_apply", toks), target:GibberishAES.enc('CORRECTION', toks)},
        success: function(msg){
            $("#myModal").html(msg);
        }
    }); 
});

function view_leave_status(){
    $("#manageleave").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");    
    $.ajax({
        url: "<?=site_url('process_/view_ob_status')?>",
        type: "POST",
        data: {
          toks:toks,
          category : GibberishAES.enc($("#category").val(), toks), 
          ltype: GibberishAES.enc('other', toks), 
          othtype : GibberishAES.enc('CORRECTION',toks), 
          dfrom : GibberishAES.enc($("input[name='ldfrom']").val(), toks), 
          dto : GibberishAES.enc($("input[name='ldto']").val(), toks), 
          deptid : GibberishAES.enc($("#deptid").val(), toks), 
          noDA : GibberishAES.enc('', toks)
        },
        success: function(msg){
            $("#manageleave").html(msg);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#manageleave").find(".btn").css("pointer-events", "none");
            else $("#manageleave").find(".btn").css("pointer-events", "");
        }
    });
}

$(".date").datetimepicker({
    format: 'YYYY-MM-DD'
});

$(".chosen").chosen();

$("#process_adj").click(function(){
   $.ajax({
    url: "<?=site_url('application_adj_/loadOBAdjustment')?>", 
    data : {ob_type:"CORRECTION"},
    type: "POST",
    success: function(msg){
        $("#myModal").html(msg);
    }
   });
});
</script> 
