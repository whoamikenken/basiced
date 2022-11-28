<?php

/**
 * @author Justin
 * @copyright 2016
 */

$curr_date = date('Y-m-d');
?>

<style type="text/css">
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
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Overtime Management</b></h4></div>
                   <div class="panel-body"> 
                        <div class="form_row">           
                            <div class="field no-search">
                            <!-- <div class="dark_navigation"><a id="newrequestot" href="#" data-toggle="modal" data-target="#myModal" class="glyphicon glyphicon-plus-sign btn btn-primary" style="margin-left: 15px;"> Add New</a></div> -->
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
                        </div>
                        <div class="form_row">
                            <label class="field_name align_right">Department</label>
                            <div class="field">
                                <div class="col-md-6">
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
                                <div class="dark_navigation" style="margin-bottom: 30px;">
                                    <a href="#" class="btn btn-primary" id="searchlbtn" style="margin-left: 15px;">Search</a>
                                    <a id="newrequestot" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-primary" style="margin-left: 15px;"> Apply Overtime</a>
                                </div>
                            </div>
                        </div>  
                    </div>
                </div>  
                <div id="removeAni" class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b id="overtime_heading">LIST</b></h4></div>
                   <div class="panel-body">
                    <div id="manageovertime"></div> 
                </div>
            </div>
        </div>
    </div>    
</div>
<div class="modal fade" id="myModal" data-backdrop="static"></div>
<script>
var toks = hex_sha512(" ");
if("<?=$this->session->userdata('canwrite')?>" == 0) $("#newrequestot").css("pointer-events", "none");
else $("#newrequestot").css("pointer-events", "");
$("#searchlbtn").click(function(){
    view_overtime_status();
});

    setTimeout(
      function() 
      {
        $("#removeAni").removeClass("animated fadeIn delay-1s");
      }, 2000);

$("#newrequestot").click(function(){  
    $.ajax({
        url      : "<?=site_url("process_/overtime_status")?>",
        type     : "POST",
        // data     : {folder: "process", view: "overtime_management_details"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

function view_overtime_status(){
    $("#overtime_heading").text("All Application List");
    if($("#category").val() == "PENDING") $("#overtime_heading").text("Pending Application List");
    else if($("#category").val() == "APPROVED") $("#overtime_heading").text("Approved Application List");
    else if($("#category").val() == "DISAPPROVED") $("#overtime_heading").text("Disapproved Application List");

    $("#manageovertime").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");    
    $.ajax({
        url: "<?=site_url('process_/view_overtime_status')?>",
        type: "POST",
        data: {
            toks:toks,
            category : GibberishAES.enc($("#category").val(), toks), 
            ltype: GibberishAES.enc($("#ltype").val(), toks), 
            dfrom : GibberishAES.enc($("input[name='ldfrom']").val(), toks), 
            dto : GibberishAES.enc($("input[name='ldto']").val(), toks), 
            deptid : GibberishAES.enc($("#deptid").val(), toks)
        },
        success: function(msg){
            $("#manageovertime").html(msg);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#manageovertime").find(".btn").css("pointer-events", "none");
            else $("#manageovertime").find(".btn").css("pointer-events", "");
        }
    });
}

view_overtime_status();

$("#dfrom,#ldfrom,#ldto").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();
</script> 
