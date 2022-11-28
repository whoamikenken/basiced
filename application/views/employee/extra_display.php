<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */


switch($pg){
  case "schedule" :
$sched = "";
?>
<div class="no-search">
<select class="form-control" name="schedtype">
    <?
      $opt_status = $this->extras->showadjustment_code(true);
      foreach($opt_status as $c=>$val){
      ?><option<?=($c==$sched ? " selected" : "")?> value="<?=$c?>"><?=$val?></option><?    
      }
    ?>
</select>
</div> 
<? 
  break;
}
?>
<script>
$(".chosen").chosen();
</script>