<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */



?>
<div class="form_row">
    <label class="field_name align_right">Type</label>
    <div class="field">
        <div class="col-md-4 no-search">
            <select class="form-control" name="income_base">
            <?
              $opt_status = $this->extras->showincomebase();
              foreach($opt_status as $c=>$val){
              ?><option value="<?=$c?>"><?=$val?></option><?    
              }
            ?>
            </select>
        </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">From</label>
    <div class="field">
        <div class="input-group date datefrom" data-date="<?=date("Y-m-d")?>" data-date-format="yyyy-mm-dd">
            <input class="align_center" size="16" type="text" name="datefrom" value="<?=date("Y-m-d")?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">To</label>
    <div class="field">
        <div class="input-group date dateto" data-date="<?=date("Y-m-d")?>" data-date-format="yyyy-mm-dd">
            <input class="align_center" size="16" type="text" name="dateto" value="<?=date("Y-m-d")?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Period</label>
    <div class="field">
        <div class="col-md-4 no-search">
            <select id="period_drop" name="period_drop" class="form-control">
            <?
              $opt_period = $this->extras->enum_select("cutoff_summary","cutoff_period");
              foreach($opt_period as $c=>$val){
              ?><option value="<?=$c?>"><?=$val?></option><?    
              }
            ?>
            </select>
        </div>
    </div>
</div>
<div class="form_row">
    <div class="field">
        <a href="#" class="btn btn-primary" id="save_new_cutoff">Save Cut-off</a>
    </div>
</div>

<script>
$("#save_new_cutoff").click(function(){
   /**
    if($("#sy_base").val()==""){
        alert("School year is required");
        return;
    }else if($("#sem_base").val()==""){
        alert("Semester is required");
        return;
    }
    */
   $.ajax({
       url: "<?=site_url("maintenance_/cutoff_save")?>",
       type: "POST",
       data: {
        income_base:$("select[name='income_base']").val(),
        startdate: $("input[name='datefrom']").val(),
        enddate: $("input[name='dateto']").val(),
        period: $("select[name='period_drop']").val()
       },
       success: function(msg){
          alert($(msg).find("message:eq(0)").text());
          if($(msg).find("status:eq(0)").text()==1) return;
          else{
             $("#modalclose").click();
             $(".inner_navigation .main li .active a").click();
          }
       }
    }); 
});
$(".datefrom, .dateto").datepicker({
    autoclose: true,
    todayBtn: true
});
$('.chosen').chosen();
</script>