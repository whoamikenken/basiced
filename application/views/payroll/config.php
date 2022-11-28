
<?php

/**
 * @author Justin
 * @copyright 2015
 */
/**
 * Kennedy Hipolitp
 * @2019
 * @Updated UI
 */
 
 $hc = "";
 $hc = $this->payroll->displayHeadCashier();
?>

<style>
  #income_main_acct{
    font-size: 14px;
    font-style: italic;
    font-weight: bold;
    color: #FFF;
    text-decoration: underline;
    padding-right: 20px; 
  }
  #income_main_acct:hover{
    font-style: normal;
    color: #E1BEE7;
  }
  table.table.table-striped.table-bordered.table-hover.dataTable.no-footer.dtr-inline.fixedHeader-floating {
    display: none;
  }
  table.table.table-striped.table-bordered.table-hover.dataTable.no-footer.dtr-inline.fixedHeader-locked {
    display: none;
}
        .panel {
    border: 5px solid #0072c6 !important;
    box-shadow: 0 19px 13px -4px rgba(0,0,0,0.20)!important;
    margin-bottom: 49px !important;
}
</style>
<div id="content"> <!-- Content start -->
    <div class="widgets_area">
        <div class="row">  
            <div class="col-md-12">
                <div class="panel animated fadeIn delay-1s">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Payroll Configuration</b></h4></div>
                   <div class="panel-body">
                    <!-- Income Config  -->
                  <div class="well">
                   <div class="panel-heading"><h4><b>Income Configuration</b></h4></div>
                   <div class="panel-body">
                        <div id="configincome"></div>
                    </div>
                  </div>
                    <!-- End  -->

                    <!-- Deduction Config  -->
                  <div class="well">
                   <div class="panel-heading"><h4><b>Deduction Configuration</b></h4></div>
                   <div class="panel-body">
                        <div id="configdeduc"></div>
                    </div>
                  </div>
                    <!-- End  -->
                    
                    
                    <!-- Loan Config  -->
                  <div class="well">
                   <div class="panel-heading"><h4><b>Loan Configuration</b></h4></div>
                   <div class="panel-body">
                        <div id="configloan"></div>
                    </div>
                  </div>
                    <!-- End  -->
                    
                    <!-- Other Income Config  -->
                  <div class="well" style="display: none;">
                   <div class="panel-heading"><h4><b>Other Income Configuration</b></h4></div>
                   <div class="panel-body">
                        <div id="configincomeoth"></div>
                    </div>
                  </div>
                    <!-- End  -->                                        
                    
                    <!-- Cut-off Config  -->
                  <div class="well">
                   <div class="panel-heading"><h4><b>Payroll Cut-Off Configuration</b></h4></div>
                   <div class="panel-body">
                        <div id="configcutoff"></div>
                    </div>
                  </div>
                    <!-- End  -->
                    
                    <!-- Head-Cashier Config  -->
                  <div class="well" style="display: none;">
                   <div class="panel-heading"><h4><b>Head Cashier Configuration</b></h4></div>
                   <div class="panel-body">
                        <div id="savehide" hidden=""></div>
                        <div class="col-md-3">
                        <div id="saveshow"><input type="text" name="hc" value="<?=$hc?>" placeholder="Head Cashier" class="form-control" id="headcashier" style="text-transform:uppercase; margin-right: 5px;" /><br><a class="btn btn-primary" id="savehc" href="#" class="btn btn-default">Save</a>
                        </div>
                        </div>
                    </div>
                  </div>
                    <!-- End  -->
                    
                    <!-- Recompute WithHolding Tax  -->
                  <div class="well" style="display: none;">
                   <div class="panel-heading"><h4><b>Recompute Tax</b></h4></div>
                   <div class="panel-body">
                        <div>
                            <div id="cohide" hidden=""></div>
                            <select class="form-control" id="cutoffdate" style="width: 260px;margin-bottom: 10px;"><?=$this->payrolloptions->viewtaxpercutoff();?></select>
                            <a class="btn btn-primary" id="saveco" href="#" class="btn btn-default">Recompute</a>
                            <a class="btn btn-primary" id="deltax" href="#" class="btn btn-default">Delete All Existing Tax</a>
                        </div>
                    </div>
                  </div>
                    <!-- End  -->


                    <!-- Bank Config  -->
                  <div class="well">
                   <div class="panel-heading"><h4><b>Bank</b></h4></div>
                   <div class="panel-body">
                        <div id="configbank"></div>
                    </div>
                  </div>
                    <!-- End  -->
                    
                    </div>    
                    
                </div>
            </div>
        </div>        
    </div>        
</div>
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
<script>

  var toks = hex_sha512(" ");

$(document).ready(function(){
    loaddeducconfig();          //  load deduction config
    loadincomeconfig();         //  load income config
    loadloanconfig();           //  load loan config
    loadcutoffconfig();         //  load cut-off config
    // loadincomeothconfig();     //  load other income config    
    loadbankconfig();
    // validateCanWrite();
});

/*
 *  FUNCTIONS
 */
 // deduction config
 function loaddeducconfig(){ 
    $("#configdeduc").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("payroll_/payrollconfig")?>",
       type     :   "POST",
       data     :   {view   :   GibberishAES.enc("configdeduc", toks), toks: toks},
       success  :   function(msg){
        $("#configdeduc").html(msg);
        if("<?=$this->session->userdata('canwrite')?>" == 0) $("#configdeduc").find(".btn").css("pointer-events", "none");
        else $("#configdeduc").find(".btn").css("pointer-events", "");
       }
    });
 }
 // income config
 function loadincomeconfig(){
    $("#configincome").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("payroll_/payrollconfig")?>",
       type     :   "POST",
       data     :   {view   :   GibberishAES.enc("configincome", toks), toks: toks},
       success  :   function(msg){
        $("#configincome").html(msg);
        if("<?=$this->session->userdata('canwrite')?>" == 0) $("#configincome").find(".btn").css("pointer-events", "none");
        else $("#configincome").find(".btn").css("pointer-events", "");
       }
    });
 }
 // loan config
 function loadloanconfig(){
    $("#configloan").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("payroll_/payrollconfig")?>",
       type     :   "POST",
       data     :   {view   :   GibberishAES.enc("configloan", toks), toks: toks},
       success  :   function(msg){
        $("#configloan").html(msg);
        if("<?=$this->session->userdata('canwrite')?>" == 0) $("#configloan").find(".btn").css("pointer-events", "none");
        else $("#configloan").find(".btn").css("pointer-events", "");
       }
    });
 }
 // income config
 function loadincomeothconfig(){
    $("#configincomeoth").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("payroll_/payrollconfig")?>",
       type     :   "POST",
       data     :   {view   :   GibberishAES.enc("configincomeoth", toks), toks: toks},
       success  :   function(msg){
        $("#configincomeoth").html(msg);
        if("<?=$this->session->userdata('canwrite')?>" == 0) $("#configincomeoth").find(".btn").css("pointer-events", "none");
        else $("#configincomeoth").find(".btn").css("pointer-events", "");
       }
    });
 } 

  $('#income_main_acct').on('click',function(){
    $.ajax({
       url      :   "<?=site_url("payroll_/getIncomeSetupMainAccountList")?>",
       type     :   "POST",
       success  :   function(msg){
        $("#myModal").html(msg);
       }
    });
  });

 // cut-off config
 function loadcutoffconfig(){
    $("#configcutoff").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("payroll_/payrollconfig")?>",
       type     :   "POST",
       data     :   {view   :   GibberishAES.enc("configcutoff", toks), toks: toks},
       success  :   function(msg){
        $("#configcutoff").html(msg);
        if("<?=$this->session->userdata('canwrite')?>" == 0) $("#configcutoff").find(".btn").css("pointer-events", "none");
        else $("#configcutoff").find(".btn").css("pointer-events", "");
       }
    });
 }

  // Bank config
function loadbankconfig(){
    $("#configbank").html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
    $.ajax({
       url      :   "<?=site_url("payroll_/payrollconfig")?>",
       type     :   "POST",
       data     :   {view   :   GibberishAES.enc("configbank", toks), toks: toks},
       success  :   function(msg){
        $("#configbank").html(msg);
        if("<?=$this->session->userdata('canwrite')?>" == 0) $("#configbank").find(".btn").css("pointer-events", "none");
        else $("#configbank").find(".btn").css("pointer-events", "");
       }
    });
}

function validateCanWrite(){
    if("<?=$this->session->userdata('canwrite')?>" == 0) $("input, .btn").css("pointer-events", "none");
    else $("input, .btn").css("pointer-events", "");
}

 // head cashier config
 $("#savehc").click(function(){
    $("#savehide").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
    $("#saveshow").hide();
    $.ajax({
       url     :    "<?=site_url("payroll_/loadmodelfunc")?>",
       type    :    "POST",
       data    :    {model  :   "hcsave",   headcashier :   $("#headcashier").val()},
       success :    function(msg){
        alert(msg);
        $("#savehide").hide();
        $("#saveshow").show();
       }   
    });
 });
 //Recompute Tax
 $("#saveco").click(function(e){
    var conf = confirm("Are you sure you want to Recompute?");
    if(conf){
    $("#cohide").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
    $("#saveco").hide();
    $.ajax({
       url     :    "<?=site_url("payroll_/loadmodelfunc")?>",
       type    :    "POST",
       data    :    {model  :   "recomputetax",   cutoffdate :   $("#cutoffdate").val()},
       success :    function(msg){
        alert(msg);
        $("#cohide").hide();
        $("#saveco").show();
       }   
    });
    e.preventDefault();
    }else   return false;
 });
 // Delete Existing Tax
 $("#deltax").click(function(e){
    var conf = confirm("Are you sure you want to delete?");
    if(conf){
    $("#cohide").show().html("<img src='<?=base_url()?>images/loading.gif' />  Saving, Please Wait..");
    $("#saveco,#deltax").hide();
    $.ajax({
       url     :    "<?=site_url("payroll_/loadmodelfunc")?>",
       type    :    "POST",
       data    :    {model  :   "deletetax",   cutoffdate :   $("#cutoffdate").val()},
       success :    function(msg){
        alert(msg);
        $("#cohide").hide();
        $("#saveco,#deltax").show();
       }   
    });
    e.preventDefault();
    }else   return false;
 });
 $(".chosen").chosen();
</script>