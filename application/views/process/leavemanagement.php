<?php

/**
 * @author Justin
 * @copyright 2015
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
        margin-left: 0px
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
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Leave Management</b></h4></div>
                   <div class="panel-body"> 
                   <br> 
                        <div class="form_row ">           
                            <!-- <div class="field no-search"> -->
                                <!-- <div class="dark_navigation"><a href="#modal-view" tag='add_d' data-toggle="modal" class="glyphicon glyphicon-plus-sign btn blue"> Add New</a></div> -->
                            <label class="field_name align_right">Status</label>             
                            <div class="field no-search" >
                                <div class="col-md-6" >
                                    <select class="select blue chosen " id="categorystat">
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
                                <span class="pull-right " id="wrap_adj"><a href="#" data-toggle="modal" data-target="#myModal" id="process_adj" style="color: black;">Process Adjustment</a></span>   
                            <!-- </div> -->
                        </div> 
                        <div class="form_row ">           
                            <label class="field_name align_right">Leave type</label>             
                            <div class="field no-search" >
                                <div class="col-md-6" >
                                    <select class="select blue chosen" id="leavetype">
                                <option value="">- All leave type -</option>
                                <?
                                    $opt = $this->extras->showUpdatedLeavelist();
                                    foreach($opt as $key=>$val){
                                ?>      
                                        <option value="<?=$val['code_request']?>"><?=$val['description']?></option><?
                                    }
                                ?>
                                </select>
                                </div>  
                            </div>
                        </div>                        
                        <div class="form_row" hidden>        
                            <label class="field_name align_right" hidden>Leave Type</label>
                            <div class="field no-search">  
                                <select class="select blue chosen" id="leavetype">
								<option value="">- All leave type -</option>
                                <?
                                    $opt = $this->extras->showLeavelist();
                                    foreach($opt as $key=>$val){
                                ?>      
                                        <option value="<?=$key?>"><?=$val?></option><?
                                    }
                                ?>
                                </select>
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
                                <div class="col-md-12" style="padding-left: 0px;">
                                  <div class="col-md-3">
                                  <div class='input-group date' id="ldfrom" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="ldfrom" value="<?=$curr_date?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                              <div class="col-md-3" style="margin-left:8px;">
                                <div class='input-group date' id="ldto" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                    <input type='text' class="form-control"  size="16" name="ldto" type="text" value="<?=$curr_date?>"/>
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
                                <a href="#" class="btn btn-primary" id="searchlbtn" style="margin-left: 15px;">Search</a>
                                <a class="btn btn-primary" tag='add_d' id="applyleave" href="#" data-toggle="modal" data-target="#myModal" class="btn btn-default" style="margin-left: 15px;">Apply Leave</a>
                            </div>
                        </div>
                        <br>  
                    </div>
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                </div>
                <div id="manageleave"></div>     
            </div>
        </div>
    </div>    
</div>
<script>
var toks = hex_sha512(" ");
$("#searchlbtn").click(function(){
    view_leave_status();
});

$("a[tag='add_d']").click(function(){
   $("#modal-view").find("h3[tag='title']").text("Add Leave Status");
   $("#button_save_modal").text("Save");  
   
   $.ajax({
    //url: "<?=site_url('process_/leave_status')?>", // comment for ica-hyperion 21194
    url: "<?=site_url('employeemod_/fileconfig')?>", // new added for ica-hyperion 21194
    type: "POST",
    data : {toks:toks, job : GibberishAES.enc("add", toks), folder: GibberishAES.enc("employeemod", toks), view: GibberishAES.enc("leave_app/leave_apply", toks)}, // updated for ica-hyperion 21194
    success: function(msg){
        // $("#modal-view").find("div[tag='display']").html(msg);
        $("#myModal").html(msg);
    }
   });
});

$("#process_adj").click(function(){
   $.ajax({
    url: "<?=site_url('application_adj_/loadLeaveAdjustment')?>", 
    type: "POST",
    success: function(msg){
        $("#myModal").html(msg);
    }
   });
});

function view_leave_status(){
    $("#manageleave").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");    
    $.ajax({
        url: "<?=site_url('process_/view_leave_status')?>",
        type: "POST",
        data: {
            toks:toks,
            category : GibberishAES.enc($("#categorystat").val(), toks), 
            ltype: GibberishAES.enc($("#leavetype").val(), toks), 
            dfrom : GibberishAES.enc($("input[name='ldfrom']").val(), toks), 
            dto : GibberishAES.enc($("input[name='ldto']").val(), toks), 
            deptid : GibberishAES.enc($("#deptid").val(), toks), 
            noDA : GibberishAES.enc('true', toks)
        },
        success: function(msg){
            $("#manageleave").html(msg);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#manageleave").find(".btn").css("pointer-events", "none");
            else $("#manageleave").find(".btn").css("pointer-events", "");
        }
    });
}

if("<?=$this->session->userdata('canwrite')?>" == 0) $("#applyleave, #wrap_adj").css("pointer-events", "none");
else $("#applyleave, #wrap_adj").css("pointer-events", "");

$("#dfrom,#ldfrom,#ldto").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();
</script> 
