<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
?>
<style type="text/css">

    .panel {
        border: 5px solid #0072c6 !important;
        box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
        margin-bottom: 49px !important;
    }

    .form_row{
        padding-bottom: 10px;
    }

</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Processed Payroll</b></h4></div>
                   <div class="panel-body">
                        <form id="payrollform">
                            <div style="display: flex;">
                                <div style="width: 45%;">
                                    <input type="hidden" name="view" value="payrolllistview" />
                                    <input type="hidden" name="model" value="computedpayroll" />
                                    <div class="form_row">
                                        <label class="field_name align_right">Campus:</label>
                                        <div class="field">
                                            <select class="chosen col-md-4" name="campusid" style="pointer-events: none;">
                                                <?
                                                    $opt_department = $this->extras->showcampus("");
                                                    foreach($opt_department as $c=>$val){
                                                ?>      <option value="<?=$c?>" selected><?=$val?></option><?
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form_row">
                                        <label class="field_name align_right">Department:</label>
                                        <div class="field">
                                            <select class="chosen col-md-4" name="deptid">
                                                <option value="">All Department</option>
                                                <?
                                                    $opt_department = $this->extras->showdepartment("");
                                                    foreach($opt_department as $c=>$val){
                                                ?>      <option value="<?=$c?>"><?=$val?></option><?
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form_row">
                                        <label class="field_name align_right">Office:</label>
                                        <div class="field">
                                            <select class="chosen col-md-4" name="office">
                                                <option value="">All Office</option>
                                                <?php 
                                                $opt_department = $this->extras->showoffice();
                                                foreach($opt_department as $c=>$val): ?>
                                                    <option value="<?=$c?>"><?=$val?></option>
                                                <?php endforeach ?>
                                                
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form_row">
                                        <label class="field_name align_right">Employee:</label>
                                        <div class="field">
                                            <select class="chosen col-md-4" name="employeeid">
                                                <option value="">All Employee</option>
                                                <?
                                                    $opt_type = $this->employee->loadallemployee();
                                                    foreach($opt_type as $val){
                                                ?>      <option value="<?=$val['employeeid']?>"><?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?></option><?    
                                                    }
                                                ?>
                                            </select>
                                        </div>
                                    </div>    
                                </div>
                                <div style="width: 55%;">
                                    <div class="form_row no-search">
                                        <label class="field_name align_right">Schedule:</label>
                                        <div class="field">
                                            <select class="chosen col-md-4 align_left isreq" data-placeholder="No Option Available" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule("");?></select><span class="isrequired" hidden=""></span>
                                        </div>
                                    </div>
                                    <div class="form_row no-search">
                                        <label class="field_name align_right col-md-1">Payroll Cut-Off:</label>
                                        <div class="field">
                                            <div id="qhide" hidden=""></div><div id="qshow"><select class="chosen col-md-4 isreq" data-placeholder="No Option Available" id="payrollcutoff" name="payrollcutoff"></select><span class="isrequired" hidden=""></span></div>
                                        </div>
                                    </div>
                                    <div class="form_row no-search">
                                        <label class="field_name align_right col-md-1">Quarter:</label>
                                        <div class="field">
                                            <div id="quhide" hidden=""></div><div id="qushow"><select class="chosen col-md-4 align_left isreq" data-placeholder="No Option Available" name="quarter" id="quarter"></select><span class="isrequired" hidden=""></span></div>
                                        </div>
                                    </div>
                                    <div class="form_row no-search">
                                        <label class="field_name align_right col-md-1">Bank:</label>
                                        <div class="field">
                                            <select class="chosen col-md-4 align_left isreq" data-placeholder="No Option Available" name="bank" id="bank"><?=$this->payrolloptions->getBankListSelect();?></select>
                                        </div>
                                    </div>
                                    <div class="form_row no-search">
                                        <div class="field" id="btnshow">
                                            <a href="#" class="btn btn-primary" id="display_payroll">View Payroll</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div id="payrolllist"></div><br />
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
var toks = hex_sha512(" ");
$("#schedule").change(function(){
    $("#qushow,#savebtn,#btnshow").hide();
    $("#quhide").show().html('<img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</img>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          toks : toks,
          schedule  :   GibberishAES.enc($(this).val(), toks), 
          model     :   GibberishAES.enc("quarterpayroll", toks)
        },
        success: function(msg){
           // alert(msg);
           $("#quhide").hide();
           $("select[name='quarter']").html(msg).trigger("chosen:updated");
           $("#qushow,#savebtn,#btnshow").show();
        }
    });
    $("#qshow").hide();
    $("#qhide").show().html('<img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</img>');
    $.ajax({
        url     : "<?=site_url('payroll_/loadpayrollcutoff')?>",
        type    : "POST",
        data    : {
                    toks: toks,
                    schedule  :   GibberishAES.enc($(this).val(), toks), 
                    model     :   GibberishAES.enc("displaypayrollcutoffdata", toks)
                  },
        success: function(msg){
            // console.log(msg);
               // alert(msg);
           $("#qhide").hide();
           $("select[name='payrollcutoff']").html(msg).trigger("chosen:updated");
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
          toks: toks,
          schedule  :   GibberishAES.enc($("#schedule").val(), toks),   
          cutoffdate  :   GibberishAES.enc($(this).val(), toks), 
          model     :   GibberishAES.enc("quarterpayroll", toks)
        },
        success: function(msg){
           $("#quhide").hide();
           $("select[name='quarter']").html(msg).trigger("chosen:updated");
           $("#qushow,#savebtn,#btnshow").show();
        }
    });
});

$("#display_payroll").click(function(){

    if(!$('#bank').val()){
      Swal.fire({
          icon: 'warning',
          title: 'Warning!',
          text: 'Bank is required.',
          showConfirmButton: true,
          timer: 2000
      });
      return;
    }

   var form_data = $("#payrollform").serialize();
   // console.log(form_data);
   if($("#schedule").val() == "" || $("#payrollcutoff").val() == "" || $("#quarter").val() == "" || $("#schedule").val() == null || $("#payrollcutoff").val() == null || $("#quarter").val() == null){
        $(".isreq").each(function(){
           if($(this).val() == "" || $(this).val() == null) $(this).parent().parent().find('span.isrequired').html(" This field is required..").css("color","red").show();
           else                                             $(this).parent().parent().find('span.isrequired').hide();
        });
        return false;
   }else{
        $(".isrequired").hide();
        loadpayroll(form_data);
        
   }
});

$("select[name='deptid']").change(function(){
    $.ajax({
        url : "<?=site_url('setup_/getOffice')?>",
        type: "POST",
        data: {toks:toks,department:GibberishAES.enc($(this).val(), toks)},
        success: function(msg){
            if(typeof $j === "undefined") $("select[name='office']").html(msg).trigger("chosen:updated");
            else $j("select[name='office']").html(msg).trigger("chosen:updated");
        }
    });
});

$("select[name='office'], select[name='deptid']").on('change',function(){
    var office = $("select[name='office']").val();
    var deptid = $("select[name='deptid']").val();
    $.ajax({
        type : "POST",
        url: "<?=site_url('payroll_/employeeDropdown')?>",
        data: {toks:toks,deptid:GibberishAES.enc(deptid, toks),office:GibberishAES.enc(office, toks)},
        success: function(data){
            $("select[name='employeeid']").html(data).trigger("chosen:updated");
        }
    });
});

function loadpayroll(form_data){
    $("#payrolllist").show().html('<img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</img>');
    $.ajax({
        url      :   "<?=site_url("payroll_/loadProcessedPayrollSummary")?>",
        // url      :   "<?=site_url("payroll_/payrollconfig")?>",
        type     :   "POST",
        data     :   {toks:toks,formdata:GibberishAES.enc(form_data, toks)},
        success  :   function(msg){
            $("#payrolllist").show().html(msg);
        }
    });
}

$(".chosen").chosen();
</script>