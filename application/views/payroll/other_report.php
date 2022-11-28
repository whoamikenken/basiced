<?php  
/**
* @author justin (with e)
* @copyright 2018
*/

$filter_adjustment_history = $this->reports->getFilterHistory("adjustment");
$filter_adjustment_code = explode(",", $filter_adjustment_history);

$filter_deduction_history = $this->reports->getFilterHistory("deduction");
$filter_deduction_code = explode(",", $filter_deduction_history);

?>
<style type="text/css">
  .form_row{
    padding-bottom: 15px;
  }
</style>

<div class="modal-dialog">
  <div class="modal-content">
    <div class="modal-header">
            <div class="media">
          <div class="media-left">
            <img src="<?=base_url()?>images/school_logo.png" class="media-object" style="width:60px">
          </div>
          <div class="media-body" style="font-weight: bold;padding-top: 10px;">
            <h4 class="media-heading" style="font-family: Avenir;"><b>Pinnacle Technologies Inc.</b></h4>
                        <p style="font-family: Avenir;  margin-top: -1.5%;">D`Great</p>
          </div>
        </div>
        <center><b><h3 tag="title" class="modal-title">Setup</h3></b></center>
        </div>
        <form id="report_frm">
          <input type="hidden" name="reportname">
          <input type="hidden" name="emp_bank">
        <div class="modal-body">
          <div class="content">
          <?
            if($report == "payroll_registrar" || $report == "payslip-report" || $report == "atm-payroll-report"){
          ?>
            <div class="form_row">
                    <label class="field_name align_right">Department:</label>
                    <div class="field">
                        <select class="chosen col-md-4" name="deptid" id="deptid">
                            <option value="">All Department</option>
                            <?
                                $opt_department = $this->extras->showdepartment();
                                foreach($opt_department as $c=>$val){
                            ?>      <option value="<?=$c?>"><?=$val?></option><?
                                }
                            ?>
                        </select>
                    </div>
                </div>
                <?php if($report == "payroll_registrar" || $report == "atm-payroll-report"){ ?>
                <div class="form_row">
                    <label class="field_name align_right">Campus:</label>
                    <div class="field">
                        <select class="chosen col-md-4" name="campusid" id="campusid">
                            <option value="">All Campus</option>
                            <?
                                $opt_department = $this->extras->showcampus();
                                foreach($opt_department as $c=>$val){
                            ?>      <option value="<?=$c?>"><?=$val?></option><?
                                }
                            ?>
                        </select>
                    </div>
                </div>
              <?php }else{ ?>
                <div class="form_row">
                    <label class="field_name align_right">Office:</label>
                    <div class="field">
                        <select class=" chosen" id="office" name="office"><?=$this->extras->getOffice()?></select>
                    </div>
                </div>
              <?php } ?>
                <div class="form_row" <?= ($report == "payroll_registrar") ? "" : "hidden" ?>>
                    <label class="field_name align_right">Teaching Type</label>
                    <div class="field">
                        <select class="chosen col-md-4" name="teachingtype" id="teachingtype">
                          <option value="">All Teaching Type</option>
                          <option value="teaching">Teaching</option>
                          <option value="nonteaching">Non Teaching</option>
                        </select>
                    </div>
                </div>
                <div class="form_row">
                  <label class="field_name align_right">Employee:</label>
            <div class="field"  id="allemp">
            <select class="chosen col-md-4" name="employeeid">
              <option value="">All Employee</option>
              <?
              $opt_type = $this->employee->loadallemployee("",array(array("lname","asc"),array("fname","asc"),array("mname","asc")));
              foreach($opt_type as $val){
              ?>      
              <option value="<?=$val['employeeid']?>">
                <?=($val['employeeid'] . " - " . $val['lname'] . ", " . $val['fname'] . " " . $val['mname'])?>
                  
              </option>
              <?    
              }
              ?>
            </select>
            </div>
          </div>
          <div class="form_row no-search">
                    <label class="field_name align_right">Schedule:</label>
                    <div class="field">
                        <select class="chosen col-md-4 align_left isreq" data-placeholder="No Option Available" name="schedule" id="schedule"><?=$this->payrolloptions->payschedule($schedule);?></select><span class="isrequired" hidden=""></span>
                    </div>
                </div>
                <div class="form_row no-search">
                    <label class="field_name align_right">Payroll Cut-Off:</label>
                    <div class="field">
                        <div id="qhide" hidden=""></div><div id="qshow"><select class="chosen col-md-4 isreq" data-placeholder="No Option Available" id="payrollcutoff" name="payrollcutoff"></select><span class="isrequired" hidden=""></span></div>
                    </div>
                </div>
                <div class="form_row no-search">
                    <label class="field_name align_right">Quarter:</label>
                    <div class="field">
                        <div id="quhide" hidden=""></div><div id="qushow"><select class="chosen col-md-4 align_left isreq" data-placeholder="No Option Available" name="quarter" id="quarter"></select><span class="isrequired" hidden=""></span></div>
                    </div>
                </div>
                <div class="form_row no-search">
                    <label class="field_name align_right">Status:</label>
                    <div class="field">
                        <select class="chosen col-md-4" name="payroll_status">
                          <option value="PROCESSED">PROCESSED</option>
                          <option value="PENDING">PENDING</option>
                          <option value="SAVED">SAVED</option>
                        </select>
                    </div>
                </div>
          <?
            }

            if($report == "payroll_registrar"){
          ?>
            
            <div class="form_row no-search wrapSaved">
                <label class="field_name align_right">Bank:</label>
                <div class="field">
                    <select class="chosen col-md-4 align_left isreq" data-placeholder="No Option Available" name="bank" id="bank">
                      <?=$this->payrolloptions->getBankListSelect();?>
                    </select>
                </div>
            </div>
            <div class="form_row" id="sort_div">
              <label class="field_name align_right">Sort:</label>
              <div class="field no-search">
                        <select class="chosen col-md-4 align_left isreq" name="sort" data-placeholder="No Option Available" name="sort" id="sort">
                          <!-- <option value="name">Name</option>
                          <option value="department">Campus per Dept</option>
                          <option value="campus">Campus</option> -->
                          <option value="name">Alphabethical</option>
                          <!-- <option value="department">Office</option> -->
                        </select>
                    </div>
            </div>
            <div class="form_row" id="sd_filter_div">
              <label class="field_name align_right">&nbsp;</label>
              <div class="field no-search">
                        <select class="chosen col-md-4 align_left isreq" name="sd_filter" data-placeholder="No Option Available" name="bank" id="bank">
                          <option value="detailed">Detailed</option>
                          <option value="summary">Summary</option>
                        </select>
                    </div>
            </div>
                <div class="form_row">
                  <label class="field_name align_right">Deminimis</label>
                    <div class="field no-search">
                        <select class="chosen col-md-4 align_left" name="demchoices">
                            <option value="" selected>Select your choice</option>
                            <option value="yes">Yes</option>
                            <option value="no">No</option>
                        </select>
                    </div>
                </div>
                <?
                    $deminimiss = $this->reports->getPayrollIncomeConfigDeminimis("payroll_income_config","selectAll");
                    $notdeminimiss = $this->reports->getPayrollIncomeConfigNoDeminimis("payroll_income_config","selectAll");
                    $alldeminimiss = $this->reports->getPayrollIncomeConfig("payroll_income_config","selectAll");
                ?>
                 <div class="form_row" id="incomecategory" hidden>
                          <label class="field_name align_right">Income:</label>
                          <div class="field no-search">
                              <select class="chzn-select col-md-4 income-list" multiple name="income[]" id="income">
                                <!-- insert option here -->
                              </select>
                          </div>
                      </div>
                         <?
                    $deductionconfig = $this->reports->getPayrollIncomeConfig("payroll_deduction_config","selectAll");
                ?>
                  <div class="form_row" id="adjustmentcategory" hidden>
                          <label class="field_name align_right">Adjustment:</label>
                          <div class="field no-search" id="adjustmentfield" hidden>
                              <select class="chzn-select col-md-4" multiple name='adjustment[]' id="adjustment">
                                <?foreach ($alldeminimiss as $key => $allvalue) {?>
                                      <option type='checkbox' value='<?=$allvalue["id"]?>' <?= (in_array($allvalue["id"], $filter_adjustment_code)) ? "selected" : "" ?> ><?=$allvalue["description"]?></option>
                                    <?}?>
                              </select>
                          </div>
                  </div>

                  <div class="form_row" id="deductioncategory" hidden>
                          <label class="field_name align_right">Deduction:</label>
                          <div class="field no-search" id="deducfield" hidden>
                              <select class="chzn-select col-md-4" multiple name='deduction[]' id="deduction">
                                <option type='checkbox' value='selectalldeduction'>Select All</option>
                                <?foreach ($deductionconfig as $key => $deducvalue) {?>
                                      <option type='checkbox' value='<?=$deducvalue["id"]?>' <?= (in_array($deducvalue["id"], $filter_deduction_code)) ? "selected" : "" ?>  ><?=$deducvalue["description"]?></option>
                                    <?}?>
                              </select>
                          </div>
                  </div>

            <div class="form_row" id="format_div">
              <label class="field_name align_right">&nbsp;</label>
              <div class="field">
                        <input type="radio" name="format" value="PDF" checked="">&nbsp;PDF &nbsp;&nbsp;
                      <input type="radio" name="format" value="XLS">&nbsp;EXCEL 
                    </div>
            </div>
        <?
          }
          if($report == "payslip-report" || $report == "atm-payroll-report") :
        ?>

                <div class="form_row no-search">
                    <label class="field_name align_right">Bank:</label>
                    <div class="field">
                        <select class="chosen col-md-4 align_left isreq" data-placeholder="No Option Available" name="bank" id="bank">
                          <?=$this->payrolloptions->getBankListSelect();?>
                        </select>
                    </div>
                </div>
                                <div class="form_row" id="sort_div">
                  <label class="field_name align_right">Sort:</label>
                    <div class="field">
                      <input type="radio" name="sort" value="name" checked="">&nbsp;Alphabethical &nbsp;&nbsp;
                    </div>
              </div>
                <div class="form_row" id="format_div">
                    <label class="field_name align_right">&nbsp;</label>
                    <div class="field">
                      <input type="radio" name="reportformat" value="PDF" checked="">&nbsp;PDF &nbsp;&nbsp;
                      <?php if($report != "payslip-report"){  ?>
                      <input type="radio" name="reportformat" value="XLS" >&nbsp;EXCEL 
                    <?php } ?>
                    </div>
                </div>
        <?
          endif;
        ?>
          </div>
        </div>
        </form>

        <div class="modal-footer">
            <div id="loading" hidden=""></div>
            <div id="saving">
                <button type="button" id="close" class="btn btn-danger" data-dismiss="modal">Close</button>
                <button type="button" id="gen" class="btn btn-success">Generate</button>
            </div>
        </div>
  </div>
</div>

<script type="text/javascript">
   var toks = hex_sha512(" ");
   $("#deptid, #campusid, #teachingtype, #office").unbind().change(function(){
    var deptid = $("#deptid").val();
    var campusid = $("#campusid").val();
    var etype = $("#teachingtype").val();
    var office = $("#office").val();
    $.ajax({
      type:'POST',
      url:"<?=site_url('reports_/loadEmployee')?>",
      data: {toks:toks,deptid:GibberishAES.enc(deptid, toks), campusid:GibberishAES.enc(campusid, toks), etype:GibberishAES.enc(etype, toks), officeid:GibberishAES.enc(office, toks)},
      success:function(html){
          $("select[name='employeeid']").html(html).trigger('chosen:updated');
      }
    }); 
  });

$("#income").on("change", function(){
  var elementId = $(this).attr("id");
  
        var value = $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").show();});
});

$("#deduction").on("change", function(){
  var elementId = $(this).attr("id");
  
        $("#"+elementId).parent().find('#'+elementId+"_chzn").find(".chzn-drop").find("ul").each(function(){ $(this).find(".active-result").show();});
});

$("select[name=demchoices]").change(function(){

        if($(this).val() == 'yes'){
            $("#incomecategory").show();
            $("#deductioncategory").show();
            $("#adjustmentcategory").show();

            $("#deductioncategory").find("#deducfield").show();
            $("#adjustmentcategory").find("#adjustmentfield").show();

        }
        else{
            $("#incomecategory").show();
            $("#deductioncategory").show();
            $("#adjustmentcategory").show();

            $("#deductioncategory").find("#deducfield").show();
            $("#adjustmentcategory").find("#adjustmentfield").show();

        }

        $.ajax({
          url : "<?=site_url("reports_/getIncomeOptions")?>",
          type : "POST",
          data : {toks:toks,is_deminimis : GibberishAES.enc($(this).val(), toks)},
          dataType : "json",
          success : function(result){
            var income_option = "";

            for(i in result){
              var option_tag = "<option ";
              var selected = (result[i].is_select) ? "selected" : "";

              option_tag += "value=\""+ result[i].value +"\" ";
              option_tag += selected +"> "+ result[i].caption +" ";
              option_tag += "</option>";

              income_option += option_tag;
            }

            $(".income-list").html(income_option).trigger("chosen:updated");
          }
        });
});

$('select[name=payroll_status]').change(function(){
  if($(this).val()=='PENDING'){
    $("#bank").prop('disabled', true).trigger("chosen:updated");
    $('.wrapPending').show();
    $('.wrapSaved').hide();
  }else{
    $("#bank").prop('disabled', false).trigger("chosen:updated");
    $('.wrapPending').hide();
    $('.wrapSaved').show();
  }
});
$("#schedule").change(function(){
    $("#qushow,#savebtn,#btnshow").hide();
    $("#quhide").show().html('<img src="<?=base_url()?>images/loading.gif" />Loading, Please Wait..</img>');
    $.ajax({
        url: "<?=site_url('payroll_/loadquarterforsched')?>",
        type: "POST",
        data: {
          toks      :   toks,
          schedule  :   GibberishAES.enc($(this).val(), toks), 
          model     :   GibberishAES.enc("quarterpayroll", toks)
        },
        success: function(msg){
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
                    toks      :   toks,
                    schedule  :   GibberishAES.enc($(this).val(), toks), 
                    model     :   GibberishAES.enc("displaypayrollcutoff", toks)
                  },
        success: function(msg){
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
          toks      : toks,
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

$("#deptid").change(function(){
    $.ajax({ 
        url : "<?=site_url('setup_/getOffice')?>",
        type: "POST",
        data: {department:GibberishAES.enc($(this).val(), toks), toks:toks},
        success: function(msg){
            $("#office").html(msg).trigger("chosen:updated");
        }
    });
});

$("#gen").unbind("click").click(function(){
  var payroll_status = $('select[name=payroll_status]').val();
  var payroll_bank = $("#bank").val();
  if(payroll_status != "PENDING" && !payroll_bank){
    Swal.fire({
        icon: 'warning',
        title: 'Warning!',
        text: 'Bank is required.',
        showConfirmButton: true,
        timer: 1000
    })
    return;
  }
  var formdata  = $("#report_frm").serialize();
  var report    = "<?=$report?>";
  var is_continue = true;
  var site_url  = "";

  switch(report){
    case "payroll_registrar":

      if(!$("select[name='payrollcutoff']").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Payroll Cut-Off is required.',
            showConfirmButton: true,
            timer: 1000
        })
        is_continue = false;
      }else{
        $("#report_frm").attr("target", "_blank");
        $("#report_frm").attr("action", "<?=site_url("reports_/displayPayrollRegistrarReport")?>");
        $("#report_frm").attr("method", "post");
        $("#report_frm").submit();
      }

      // site_url = "<?=site_url("reports_/displayPayrollRegistrarReport")?>";
        
      break;

    case "payslip-report":
      $("#report_frm").attr("target", "_blank");
      $("#report_frm").attr("action", "<?=site_url("forms/showPayslipReport")?>");
      $("#report_frm").attr("method", "post");
      $("#report_frm").submit();
      
      return;
      break;

    case "atm-payroll-report":
    if(!$("select[name='payrollcutoff']").val()){
        Swal.fire({
            icon: 'warning',
            title: 'Warning!',
            text: 'Payroll Cut-Off is required.',
            showConfirmButton: true,
            timer: 1000
        })
        is_continue = false;
      }else{
        $("input[name='reportname']").val("atmpayrolllist");
        $("input[name='emp_bank']").val($("select[name='bank']").val());
        $("#report_frm").attr("target", "_blank");
        $("#report_frm").attr("action", "<?=site_url("payroll_/loadPayrollReport")?>");
        $("#report_frm").attr("method", "post");
        $("#report_frm").submit();
     }
      
      return;
      break;
  }

  // if(!site_url && is_continue){
  //   Swal.fire({
  //       icon: 'warning',
  //       title: 'Warning!',
  //       text: 'Error! site_url is not declare..',
  //       showConfirmButton: true,
  //       timer: 1000
  //   })
  //   is_continue = false;
  // }

  // if(is_continue){
  //   window.open(site_url +"?"+ formdata, "Report");
  // }
});


$(".chzn-select").chosen();
$(".chosen").chosen();

</script>

