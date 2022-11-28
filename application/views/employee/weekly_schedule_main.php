<?php
    $weeklySchedules = array("weekly"=>"Weekly","1"=>"1st Week","2"=>"2nd Week","3"=>"3rd Week","4"=>"4th Week","5"=>"5th Week");
?>
<div class="container" style="width: 100%;">
   <div class="form-group">
      <label class="field_name align_right">Weekly Schedule</label>
      <div class="field">
          <select name="wSched_main" id="wSched_main" class="chosen " multiple>
            <?  
              foreach($weeklySchedules as $k=>$v)
              {
              ?>
                <option  <?= in_array($k, explode(',', $weekly_flexible))? 'selected' :""?>  value="<?=$k?>"><?=$v?></option>
              <?                    
              }
            ?>
          </select>
      </div>
  </div>
</div>

<script type="text/javascript">
  $(document).ready(function(){
    if($("select[name='wSched_main']").val()){
      if(!$("select[name='wSched_main']").val().includes("weekly")){
        $('select[name="wSched_main"] option[value="weekly"]').attr("disabled", true).trigger("chosen:updated");
        $('select[name="wSched_main"] option[value!="weekly"]').attr("disabled", false).trigger("chosen:updated");
      }else{
        $('select[name="wSched_main"] option[value!="weekly"]').attr("disabled", true).trigger("chosen:updated");
        $('select[name="wSched_main"] option[value="weekly"]').attr("disabled", false).trigger("chosen:updated");
      }
    }else{
      $('select[name="wSched_main"] option[value="weekly"]').attr("disabled", false).trigger("chosen:updated");
      $('select[name="wSched_main"] option[value!="weekly"]').attr("disabled", false).trigger("chosen:updated");
    }
});

  $("select[name='wSched_main']").change(function(){
    if($(this).val()){
      if(!$(this).val().includes("weekly")){
        $('select[name="wSched_main"] option[value="weekly"]').attr("disabled", true).trigger("chosen:updated");
        $('select[name="wSched_main"] option[value!="weekly"]').attr("disabled", false).trigger("chosen:updated");
      }else{
        $('select[name="wSched_main"] option[value!="weekly"]').attr("disabled", true).trigger("chosen:updated");
        $('select[name="wSched_main"] option[value="weekly"]').attr("disabled", false).trigger("chosen:updated");
      }
    }else{
      $('select[name="wSched_main"] option[value="weekly"]').attr("disabled", false).trigger("chosen:updated");
      $('select[name="wSched_main"] option[value!="weekly"]').attr("disabled", false).trigger("chosen:updated");
    }
  });

  $(".chosen").chosen();
</script>