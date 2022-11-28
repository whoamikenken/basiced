<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
?>
<style>
.ui-dialog .ui-dialog-titlebar.ui-widget-header{background: none; border: none; height: 20px; width: 20px; padding: 0px; position: static; float: right; margin: 0px 2px 0px 0px;}
.ui-dialog-titlebar.ui-widget-header .ui-dialog-title{display: none;}
.ui-dialog-titlebar.ui-widget-header .ui-button{background: none; border: 1px solid #CCCCCC;}
.ui-dialog .ui-dialog-titlebar .ui-dialog-titlebar-close{margin: 0px; position: static;}
.ui-dialog .dialog.ui-dialog-content{padding: 0px 10px 10px 10px;}
.ui-dialog .ui-dialog-titlebar .ui-dialog-titlebar-close .ui-icon{position: relative; margin-top: 0px; margin-left: 0px; top: 0px; left: 0px;}
.ui-dialog .ui-dialog-titlebar-close {position: absolute;right: .3em;top: 50%;width: 21px;margin: -10px 0 0 0;padding: 1px;height: 20px;display:none;}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="well blue">
                    <div class="well-header">
                        <h5>Pay Slip</h5>
                        <ul>
                            <li class="color_pick"><a href="#"><i class="glyphicon glyphicon-th"></i></a>
                                <ul>
                                    <li><a class="blue set_color" href="#"></a></li>
                                    <li><a class="light_blue set_color" href="#"></a></li>
                                    <li><a class="grey set_color" href="#"></a></li>
                                    <li><a class="pink set_color" href="#"></a></li>
                                    <li><a class="red set_color" href="#"></a></li>
                                    <li><a class="orange set_color" href="#"></a></li>
                                    <li><a class="yellow set_color" href="#"></a></li>
                                    <li><a class="green set_color" href="#"></a></li>
                                    <li><a class="dark_green set_color" href="#"></a></li>
                                    <li><a class="turq set_color" href="#"></a></li>
                                    <li><a class="dark_turq set_color" href="#"></a></li>
                                    <li><a class="purple set_color" href="#"></a></li>
                                    <li><a class="violet set_color" href="#"></a></li>
                                    <li><a class="dark_blue set_color" href="#"></a></li>
                                    <li><a class="dark_red set_color" href="#"></a></li>
                                    <li><a class="brown set_color" href="#"></a></li>
                                    <li><a class="black set_color" href="#"></a></li>
                                </ul>
                            </li>
                        </ul>
                    </div>
                    
                    <div class="well-content">
                        <form id="payrollform">
                        <input type="hidden" name="view" value="payrolllistview" />
                        <input type="hidden" name="model" value="computedpayroll" />
                        <div class="form_row">
                            <label class="field_name align_right">Employee</label>
                            <div class="field">
                                <select class="chosen col-md-4" name="employeeid" id="employeeid">
                                    <?
                                        $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")));
                                        foreach($opt_type as $val){
                                            if($this->session->userdata("username") == $val['employeeid']){
                                    ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?
                                            }    
                                        }
                                    ?>
                                </select>
                            </div>
                        </div>    
                        <div class="form_row no-search">
                            <label class="field_name align_right">Schedule</label>
                            <div class="field">
                                <select class="chosen col-md-4 align_left isreq" data-placeholder="No Option Available" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule($schedule);?></select><span class="isrequired" hidden=""></span>
                            </div>
                        </div>
                        <div class="form_row no-search">
                            <label class="field_name align_right col-md-1">Payroll Cut-Off</label>
                            <div class="field">
                                <div id="qhide" hidden=""></div><div id="qshow"><select class="chosen col-md-4 isreq" data-placeholder="No Option Available" id="payrollcutoff" name="payrollcutoff"></select><span class="isrequired" hidden=""></span></div>
                            </div>
                        </div>
                        <div class="form_row no-search">
                            <label class="field_name align_right col-md-1">Quarter</label>
                            <div class="field">
                                <div id="quhide" hidden=""></div><div id="qushow"><select class="chosen col-md-4 align_left isreq" data-placeholder="No Option Available" name="quarter" id="quarter"></select><span class="isrequired" hidden=""></span></div>
                            </div>
                        </div>
                        <div class="form_row no-search">
                            <div class="field" id="btnshow">
                                <a href="#" class="btn btn-primary" id="display_payroll">Generate Pay Slip</a>
                            </div>
                        </div>
                        <div id="payrolllist"></div><br />
                        </form>
                    </div>
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
/*
 * Jquery Functions 
 */
$("#schedule").change(function(){
    $("#qushow,#savebtn,#btnshow").hide();
    $("#quhide").show().html('<img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</img>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $(this).val(), 
          model     :   "quarterpayroll"
        },
        success: function(msg){
           $("#quhide").hide();
           $("select[name='quarter']").html(msg).trigger("liszt:updated");
           $("#qushow,#savebtn,#btnshow").show();
        }
    });
    
    $("#qshow").hide();
    $("#qhide").show().html('<img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</img>');
    $.ajax({
        url     : "<?=site_url('payroll_/loadpayrollcutoff')?>",
        type    : "POST",
        data    : {
                    schedule  :   $(this).val(), 
                    eid       :   $("#employeeid").val(),
                    model     :   "displaypayrollcutoffdata"
                  },
        success: function(msg){
           $("#qhide").hide();
           $("select[name='payrollcutoff']").html(msg).trigger("liszt:updated");
           $("#qshow").show();
        }
    });
});

$("#payrollcutoff").change(function(){
    $("#qushow,#savebtn,#btnshow").hide();
    $("#quhide").show().html('<img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</img>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          schedule  :   $("#schedule").val(),   
          cutoffdate  :   $(this).val(), 
          model     :   "quarterpayroll"
        },
        success: function(msg){
           $("#quhide").hide();
           $("select[name='quarter']").html(msg).trigger("liszt:updated");
           $("#qushow,#savebtn,#btnshow").show();
        }
    });
});

$("#display_payroll").click(function(){
    var dd = $("#payrollcutoff").val();
    var dsplit = dd.split(" ",2);
    var params = "?form=payslip";
        params += "&eid="+$("#employeeid").val();
        params += "&dept=";
        params += "&dfrom="+dsplit[0]; 
        params += "&dto="+dsplit[1];
        params += "&schedule="+$("#schedule").val();
        params += "&quarter="+$("#quarter").val();
        params += "&isr=1";
    if($("#payrollcutoff").val() == ""){
        alert("No cut-off selected");
        return false;
    } 
    var dialog = $('<b>Do you want to sort by name??</b>').dialog({
        modal: true,
        draggable: false,    
        resizable: false,
        buttons: {
            "Yes": function() {
                params += "&sort=0";
                window.open("<?=site_url("forms/loadForm")?>"+params);
            },
            "No":  function() {
                params += "&sort=1";
                window.open("<?=site_url("forms/loadForm")?>"+params);
            },
            "Cancel":  function() {
                dialog.dialog('close');
            }
        }
    }); 
});

$(".chosen").chosen();
</script>