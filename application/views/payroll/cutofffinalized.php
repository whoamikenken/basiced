<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
?>
<style type="text/css">
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
                <div class="panel">
                   <div class="panel-heading" style="background-color: #0072c6;"><h4><b>Finalize DTR</b></h4></div>
                   <div class="panel-body">
                        <div class="form_row no-search" style="margin-top: 10px;">
                            <label class="field_name align_right col-md-1" style="float: top 200px;">Type</label>
                            <div class="field">
                              <div class="col-md-4">
                                  <select class="chosen" id="tnt">
                                  <?
                                    $type = array("teaching"=>"Teaching","nonteaching"=>"Non Teaching");
                                    foreach($type as $c=>$val){
                                    ?><option value="<?=$c?>"><?=$val?></option><?
                                    }
                                  ?>
                                  </select>
                                </div>
                            </div>
                        </div>

                        <div class="form_row no-search" style="margin-top: 10px;">
                            <label class="field_name align_right col-md-1" style="float: top 200px;">Status</label>
                            <div class="field">
                              <div class="col-md-4">
                                  <select class="chosen" id="empstatus">
                                    <option value="">All Status</option>
                                    <option value="1" selected>Active</option>
                                    <option value="0">Inactive</option>
                                  </select>
                                </div>
                            </div>
                        </div>

                        <!--
                        <div class="form_row no-search" id="edept">
                            <label class="field_name align_right col-md-1">Department</label>
                            <div class="field">
                                <select class="chosen col-md-4" name="deptid">
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
                        -->
                        <div class="form_row no-search">
                            <label class="field_name align_right col-md-1">Process DTR Cut-Off Date</label>
                            <div class="field">
                              <div class="col-md-4">
                                <select class="chosen col-md-4" id="processcutoffdate"><?=$this->payrolloptions->displaycutofffinalized();?></select>
                              </div>
                            </div>
                        </div>
                    <div id="processdeductlist"></div><br />
                    </div>
                    <div class="modal fade" id="myModal" data-backdrop="static"></div>
                </div>
            </div>
        </div>        
    </div>        
</div>
<script>
var toks = hex_sha512(" ");
$("#processcutoffdate, #empstatus").change(function(){
   if($(this).val() != ""){
   $("#processdeductlist").show().html("<img src='<?=base_url()?>images/loading.gif' />  Loading, Please Wait..");
   $.ajax({
       url      :   "<?=site_url("payroll_/payrollconfig")?>",
       type     :   "POST",
       data     :   {
                        toks   :   toks,
                        cdate  :   GibberishAES.enc($("#processcutoffdate").val(), toks),
                        type   :   GibberishAES.enc($("#tnt").val(), toks),
                        empstatus   :   GibberishAES.enc($("#empstatus").val(), toks),
                        view   :   GibberishAES.enc("cutofffinalizedlist", toks)
                    },
       success  :   function(msg){
        $("#processdeductlist").html(msg);
       }
    }); 
   }else{
    $("#processdeductlist").hide();
   }
});

$("#tnt").change(function(){
   if($(this).val() == "teaching"){
    $("#estat").hide();
    $("#processdeductlist").html("");
    //$("#edept").show();
    loadempopt($(this).val());
   }else if($(this).val() == "nonteaching"){
    //$("#edept").hide();
    $("#processdeductlist").html("");
    $("#estat").show();
    loadempopt($(this).val())
   }
});
function loadempopt(etype = ""){
    $.ajax({
        url      : "<?=site_url("payroll_/loadpayrollcutoff")?>",
            type     : "POST",
            data     : {
                            toks:toks,
                            model   : GibberishAES.enc("displaycutofffinalized", toks),
                            schedule: GibberishAES.enc(etype, toks)
                        },
        success: function(msg) {
            $("#processcutoffdate").html(msg).trigger('chosen:updated');
        }
    });   
}

$(".chosen").chosen();
</script>