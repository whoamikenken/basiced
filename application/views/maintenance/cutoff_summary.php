<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */


?>
<table id="summary_list">
<?
$q = $this->db->query("select a.id,a.datefrom,a.dateto,a.cutoff_type,b.description,a.is_process 
                       from cutoff_summary a
                       INNER JOIN code_income_base b ON b.income_base=a.cutoff_type 
                       order by a.datefrom DESC");
for($c=0;$c<$q->num_rows();$c++){
 $mrow = $q->row($c);   
?>
  <tr id="<?=$mrow->id?>" isproc="<?=$mrow->is_process?>">
    <td<?if($mrow->is_process==1){?> style="background: #EDEDED;"<?}?>><?=$mrow->description?></td>
    <td<?if($mrow->is_process==1){?> style="background: #EDEDED;"<?}?>><?=date("d M Y",strtotime($mrow->datefrom))?></td>
    <td<?if($mrow->is_process==1){?> style="background: #EDEDED;"<?}?>><?=date("d M Y",strtotime($mrow->dateto))?></td>
  </tr>
<?
}
?>  
</table>
<script>
 $("#summary_list tr").click(function(){
    var ids = $(this).attr("id");
    var proc = $(this).attr("isproc");
    
    if(ids) load_detailed(ids,proc);
 });
</script>