<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */

?>
<div class="widgets_area">
<div class="row">
<div class="col-md-12">
<div class="well-content no-search">
<div class="responsive_table_scroll">
<table class="footable">
<tbody>
<?
$q = $this->db->query("select a.id,a.cdate,ifnull(a.holidays,'') as holidays,a.days_,concat(b.description,' (',b.holiday_type,')') as description,c.is_process 
                       from cutoff_details a
                       left join cutoff_summary c on c.id=a.id
                       left join code_holidays b on b.code=a.holidays
                       where a.id='{$sid}'
                       order by a.cdate");
$con = 0;$isproc = 0;                   
for($c=0;$c<$q->num_rows();$c++){
 $con++;
 $mrow = $q->row($c);
 $isproc = $mrow->is_process==1;  
?>
  <tr>
    <td class="col-md-1"><?=$con?></td>
    <td class="col-md-4"><?=date("d M D",strtotime($mrow->cdate))?></td>
    <td class="col-md-6">
      <?if($isproc){echo (trim($mrow->description) ? $mrow->description : " <i>REGULAR DAY</i>");}else{?>
       <div class="col-md-4 no-search">
            <select class="form-control" name="emptype" cdate='<?=($mrow->cdate)?>' cid='<?=($mrow->id)?>'>
            <?
              $opt_type = $this->extras->showholiday();
              foreach($opt_type as $t=>$val){
              ?><option<?=($t==$mrow->holidays ? " selected" : "")?> value="<?=$t?>"><?=strtoupper($val)?></option><?    
              }
            ?>
            </select>
        </div>
      <?}?>  
    </td>
  </tr>
<?
}
?>  
</tbody>
</table> 
</div>
</div>
</div>
</div>
</div>
<?if(!$isproc){?>
<br />
<div class="field">
    <a href="#modal-view" class="btn btn-primary" id="proccutoff">Process Cut-off</a>
</div>
<?}else{
?>
<p><i style="color: red;">This cut-off has been processed.</i></p>
<?    
}?>
<script>
$("#proccutoff").click(function(){
    var ans = confirm("Are you sure you want to continue?");
    if(ans){
    $("#modal-view").find("h3[tag='title']").text("Processing Cut-off");  
    var form_data = {
        message: "This may take a while, please wait...",
        view: "process/loading"
    };

    $.ajax({
        url: "<?=site_url('main/siteportion')?>",
        type: "POST",
        data: form_data,
        success: function(msg){
            $("#modal-view").find("div[tag='display']").html(msg);
           
            $.when($.ajax({
                url: "<?=site_url("process_/processdtr")?>",
                type: "POST",
                data: {cid:"<?=$sid?>"},
                success: function(msg) {
                  if($(msg).find("result:eq(0)").text()==0){
                     $("#modal-view").find("div[tag='display']").html($(msg).find("message:eq(0)").text());
                     return;
                  }
                }
            })).done(function(){
            $.ajax({
                    url: "<?=site_url("process_/processpayrollcutoff")?>",
                    type: "POST",
                    data: {cid:"<?=$sid?>"},
                    success: function(msg) {  
                      $("#modal-view").find("div[tag='display']").html("Done processing...");
                      $("#modalclose").delay(1200).click();
                      $(".inner_navigation .main li .active a").click();
                    }
                });
             });    
        }
    }); 
    }else return;
});
jQuery(function($) {
    $('.footable').footable();
    $('.responsive_table_scroll').mCustomScrollbar({
        set_height: 400,
        advanced:{
            updateOnContentResize: true,
            updateOnBrowserResize: true
        }
    });
});
$(".chosen").change(function(){
   var cutoffid = $(this).attr("cid");
   var cdate = $(this).attr("cdate"); 
   var holiday = $(this).val();
 //  alert(cutoffid);
   $.ajax({
     url : "<?=site_url("maintenance_/set_holiday_dates")?>",
     type: "POST",
     data: {cid : cutoffid,cdate : cdate,holiday : holiday},
     success: function(msg){
        // just save the selected field
     }
   });
});
$(".chosen").chosen();
</script>