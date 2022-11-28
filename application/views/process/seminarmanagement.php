<?php

/**
 * @author Justin
 * @copyright 2016
 */

$curr_date = date('Y-m-d');
?>
<div id="content">
    <div class="widgets_area">
        <div class="row">
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header" style="background: #343434;">
                        <h5>Seminar Management</h5>
                    </div>
                    <div class="well-content">        
                        <div class="form_row">           
                            <div class="field no-search">
                            <div class="dark_navigation"><a id="newrequestseminar" href="#" data-toggle="modal" data-target="#myModal" class="glyphicon glyphicon-plus-sign btn blue"> Add New</a></div>
                            </div>
                        </div>                        
                        <div class="form_row">           
                            <label class="field_name align_right">Status</label>             
                            <div class="field no-search">  
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
                        <div class="form_row">
                            <label class="field_name align_right">Department</label>
                            <div class="field">
                                <div class="col-md-12">
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
                                <div class="input-group date" id="ldfrom" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="ldfrom" type="text" value="<?=$curr_date?>" readonly>
                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                                <div class="input-group date" id="ldto" data-date="<?=$curr_date?>" data-date-format="yyyy-mm-dd">
                                    <input class="align_center" size="16" name="ldto" type="text" value="<?=$curr_date?>" readonly>
                                    <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
                                </div>
                            </div>
                        </div>
                        <div class="form_row">
                            <div class="field">
                                <div id="load" hidden=""></div>
                                <a href="#" class="btn btn-primary" id="searchlbtn">Search</a>
                            </div>
                        </div>  
                    </div>
                    <div id="manageseminar"></div> 
                </div>    
            </div>
        </div>
    </div>    
</div>
<div class="modal fade" id="myModal" data-backdrop="static"></div>
<script>
$("#searchlbtn").click(function(){
    view_seminar_status();
});

$("#newrequestseminar").click(function(){  
    $.ajax({
        url      : "<?=site_url("process_/seminar_status")?>",
        type     : "POST",
        data : {job : "add"},
        success: function(msg){
            $("#myModal").html(msg);
        }
    });  
});

function view_seminar_status(){
    $("#manageseminar").html("<td colspan='5' style='text-align: center'>Loading, Please Wait.. <br /> <img src='<?=base_url()?>images/loading.gif' /></td>");    
    $.ajax({
        url: "<?=site_url('process_/view_seminar_status')?>",
        type: "POST",
        data: {category : $("#category").val(), ltype: $("#ltype").val(), dfrom : $("input[name='ldfrom']").val(), dto : $("input[name='ldto']").val(), deptid : $("#deptid").val()},
        success: function(msg){
            $("#manageseminar").html(msg);
        }
    });
}

$("#dfrom,#ldfrom,#ldto").datepicker({
    autoclose: true,
    todayBtn : true
});
$(".chosen").chosen();
</script> 
