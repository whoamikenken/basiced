<?php
$cdatefrom = date("Y-m-d");
$cdateto = date("Y-m-d");
$datetoday = date("Y");
?>
<style type="text/css">
  .panel-body{
    margin-top: 30px;
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

<div id="content"> <!-- Content start -->
<div class="widgets_area">
<div class="row">
    <div class="col-md-12">
        <div class="panel animated fadeIn">
           <div class="panel-heading" style="background-color: #0072c6;"><h4 class="h4white"><b>Subject Time Configuration</b></h4></div>
           <div class="panel-body">
                <form class="form-horizontal" id="myForm">
                    <div class="form_row" >
                      <div class="col-md-7" style="margin-left: 300px;">
                        <label class="field_name align_right" style="margin-left: 10px;">Schedule Sequence</label>
                        <div class="field ">
                                <input class="form-control text-center sequence " type="number" name="sequence" id="sequence" maxlength="10" placeholder="No. of sequences" />
                            </div>
                        </div>
                    </div>
                    <div class="form_row">
                    <div class="col-md-6">
                      <label class="field_name align_right">Range From</label>
                      <div class="field ">
                              <input class="form-control text-center from" type="number" name="from" id="from" maxlength="10" placeholder="Total No. of Hours" />
                          </div>
                      </div>
                      <div class="col-md-6">
                        <label class="field_name align_right">Range To</label>
                        <div class="field">
                                <input class="form-control text-center to" type="number" name="to" id="to" maxlength="10" placeholder="Total No. of Hours" />
                            </div>
                        </div>
                    </div>
                    <div class="form_row" >
                      <div class="col-md-6">
                        <label class="field_name align_right">Tardy Start</label>
                        <div class="field">
                                <input class="form-control text-center tardy_e" type="number" name="tardy_e" id="tardy_e" maxlength="10" placeholder="Total Tardy Minutes" />
                            </div>
                        </div>
                      <div class="col-md-6">
                        <label class="field_name align_right">Absent Start</label>
                        <div class="field">
                                <input class="form-control text-center absent_e" type="number" name="absent_e" id="absent_e" maxlength="10" placeholder="Total Absent Minutes" />
                            </div>
                        </div>
                    </div>
                    <div class="form_row" >
                      <div class="col-md-6">
                        <label class="field_name align_right">Early Dismissal Start</label>
                        <div class="field">
                                <input class="form-control text-center early_d" type="number" name="early_d" id="early_d" maxlength="10" placeholder="Total Early Minutes" />
                            </div>
                        </div>
                      <div class="col-md-6">
                        <label class="field_name align_right">Year</label>
                        <div class="field">
                                <div class='input-group date' id='date_active1' data-date="" data-date-format="yyyy">
                                    <input type='text' class="form-control text-center year"  size="16" name="year" placeholder="<?=$datetoday?>" />
                                    <span class="input-group-addon">
                                        <span class="glyphicon glyphicon-calendar"></span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="form_row">
                        <div class="field align_right">
                            <div class="col-md-12">
                                <a href="#" class="btn btn-primary" id="btnsave">Save</a>
                                <div id="msgload" hidden="" style="color: red;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="displaylogs" style="padding: 5px;"></div>
</div>
</div>

<script>
$(".date").datetimepicker({
    format: "YYYY"
});

loadlogs();
validateCanWrite();
$("#btnsave").click(function(){
   if ($(".absent_e").val() == "" || $(".absent_e").val() == null ) {
    alert("Total absent of minutes is required!");
   }
   else if ($(".tardy_e").val() == "" || $(".tardy_e").val() == null ) {
    alert("Total tardy of minutes is required!");
   }
   else if ($(".early_d").val() == "" || $(".early_d").val() == null ) {
    alert("Total early of minutes is required!");
   }else if ($(".sequence").val() == "" || $(".sequence").val() == null ) {
    alert("No of sequence is required!");
   }
   else
   {
    if($("input[name='minutes']").val() != ""){
    $("#msgload").show().html("<img src='<?=base_url()?>images/loading.gif'> Saving Please wait..");
    $("#btnsave").hide();
     $.ajax({
        url: "<?=site_url("process_/earlydismissal")?>",
        type: "POST",
        data: $("#myForm").serialize(),
        success: function(msg) {
            alert(msg);
            console.log(msg);
            $("#msgload").hide();
            $('#myForm').trigger("reset");
            $("#btnsave").show();
            loadlogs();
        }
    });   
    }else{
        alert("No. of minutes is required!.");
        $("#minutes").focus();
    }   
}
});

function loadlogs(){
    $("#displaylogs").show().html("<img src='<?=base_url()?>images/loading.gif'> Loading Please wait..");
    $.ajax({
       url  :   "<?=site_url("process_/earlydismissal")?>",
       type :   "POST",
       data :   { type : "loaddata" },
       success : function(msg){
        $("#displaylogs").html(msg);
       }
    });
}

function validateCanWrite(){
    if("<?=$this->session->userdata('canwrite')?>" == 0) $("input, .btn").css("pointer-events", "none");
    else $("input, .btn").css("pointer-events", "");
}

$('.chosen').chosen();

</script>