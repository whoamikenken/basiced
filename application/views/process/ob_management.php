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
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Official Business Management</b></h4></div>
                   <div class="panel-body">
                        <div class="modal fade" id="myModal" data-backdrop="static" style="width: 100%;"></div>        
                        <br>                    
                        <div class="form_row ">           
                            <!-- <div class="field no-search"> -->
                                <!-- <div class="dark_navigation"><a href="#modal-view" tag='add_d' data-toggle="modal" class="glyphicon glyphicon-plus-sign btn blue"> Add New</a></div> -->
                            <label class="field_name align_right">Status</label>             
                            <div class="field no-search" >
                                <div class="col-md-6" >
                                    <select class="select blue chosen " id="category">
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
                                <div class="col-md-12"style="padding-left: 0px;">
                                  <div class="col-md-3">
                                  <div class='input-group date' id="ldfrom" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                        <input type='text' class="form-control" size="16" name="ldfrom" value="<?=$curr_date?>"/>
                                        <span class="input-group-addon">
                                            <span class="glyphicon glyphicon-calendar"></span>
                                        </span>
                                    </div>
                                </div>
                              <div class="col-md-3" style="margin-left: 8px;">
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
                                <a href="#" data-target="#myModal" tag='add_d' data-toggle="modal" class="btn btn-primary"  style="margin-left: 15px;"> Apply Official Business</a>
                            </div>
                        </div> <br>  
                    </div>
                </div>
                   <div id="manageleave"></div>
                </div>         
            </div>
        </div>
    </div>    
</div>
<script>
var toks = hex_sha512(" ");
if("<?=$this->session->userdata('canwrite')?>" == 0) $("a[tag='add_d'], #process_adj").css("pointer-events", "none");
else $("a[tag='add_d'], #process_adj").css("pointer-events", "");
$("#searchlbtn").click(function(){
    if(!$("input[name='ldfrom']").val() || !$("input[name='ldto']").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'All date is required!',
            showConfirmButton: true,
            timer: 1000
        });
        return false;
    }

    var d1 = new Date($("input[name='ldfrom']").val());
    var d2 = new Date($("input[name='ldto']").val());
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
        data     : {toks:toks,folder: GibberishAES.enc("employeemod", toks), view: GibberishAES.enc("ob_app/ob_apply", toks)},
        success: function(msg){
            $("#myModal").html(msg);
        }
    }); 
});

$("#process_adj").click(function(){
   $.ajax({
    url: "<?=site_url('application_adj_/loadOBAdjustment')?>", 
    data : {ob_type:"DIRECT"},
    type: "POST",
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
            othtype : GibberishAES.enc('DA', toks), 
            dfrom : GibberishAES.enc($("input[name='ldfrom']").val(), toks), 
            dto : GibberishAES.enc($("input[name='ldto']").val(), toks), 
            deptid : GibberishAES.enc($("#deptid").val(), toks), 
            noDA : ''
        },
        success: function(msg){
            $("#manageleave").html(msg);
            if("<?=$this->session->userdata('canwrite')?>" == 0) $("#manageleave").find(".btn").css("pointer-events", "none");
            else $("#manageleave").find(".btn").css("pointer-events", "");
        }
    });
}

$("#dfrom,#ldfrom,#ldto").datetimepicker({
    format: "YYYY-MM-DD"
});
$(".chosen").chosen();
</script> 
