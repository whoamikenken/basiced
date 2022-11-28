<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */

$q = $this->db->query("select b.cdate,a.timeid,a.timein,a.timeout,a.type
                       from timesheet a
                       inner join cutoff_details b ON (b.cdate=date(a.timein) OR b.cdate=date(a.timeout))
                       where b.id='{$cutoffid}' and a.userid='{$employeeid}'
                       UNION
                       select DISTINCT b.cdate,a.timeid,a.timein,a.timeout,a.type
                       from timesheet a
                       inner join employee_schedule_adjustment b ON (b.cdate=date(a.timein) OR b.cdate=date(a.timeout)) and b.employeeid=a.userid
                       where b.cutoffid='{$cutoffid}' and a.userid='{$employeeid}' ORDER BY cdate,timein,timeid");
?>
<div class="well-content" style='border: transparent !important;'>
<table class="table table-striped table-bordered table-hover datatable">
    <thead>
    <tr>
    <th>Date</th>
    <th>Time In</th>
    <th>Time Out</th>
    <th></th>
    </tr>
    </thead>
  <tbody id="idsum">
<?
$count_sheet = $q->num_rows();
if($count_sheet>0){
for($c=0;$c<$count_sheet;$c++){
    $mrow = $q->row($c);
?>
  <tr tag='rowrequest'>
    <td class="datelogdisplay" cdate='<?=$mrow->cdate?>'>
       <div class='col-md-4 no-search'>
            <select class='chosen' name='datelog' timeid='<?=$mrow->timeid?>'>";
            <?
              $opt_type = $this->extras->showcutofdatebyid($cutoffid,$employeeid);
              foreach($opt_type as $t=>$val){
            ?><option<?=($t==$mrow->cdate ? " selected" : "")?> value='<?=$t?>'><?=$val?></option><?    
              }   
            ?>  
            </select>
        </div>
    </td>
    <td class="timeindisplay">
      <div class='input-group bootstrap-timepicker'>
            <input name='timein' class='col-md-8 input-small align_center' readonly type='text' value='<?=date("h:i A",strtotime($mrow->timein))?>'/>
            <span class='add-on'><i class='glyphicon glyphicon-time'></i></span>
      </div>
    </td>
    <td class="timeoutdisplay">
      <div class='input-group bootstrap-timepicker'>
            <input name='timeout' class='col-md-8 input-small align_center' readonly type='text' value='<?=date("h:i A",strtotime($mrow->timeout))?>'/>
            <span class='add-on'><i class='glyphicon glyphicon-time'></i></span>
      </div> 
    </td>
    <td class="col-md-1 buttondisplay">
        <div class='btn-group'>
            <a class='btn' href='#' tag='add_sched'><i class='glyphicon glyphicon-plus'></i></a>
            <a class='btn' href='#' tag='delete_sched' timeid='<?=$mrow->timeid?>'><i class='glyphicon glyphicon-trash'></i></a>
        </div>
    </td>
  </tr>
<?        
    
}
}else{
?>
  <tr>
    <td colspan="4" class='align_center'><i>No existing data</i></td>
  </tr>
<?    
}
?>  
</tbody>
</table>
</div>

<?
if($count_sheet>0){
?>
<div class="field">
    <a href="#" class="btn btn-primary" id="saverequest">Save Request</a>
</div>
<?    
}
?>
<script>
$("a[tag='delete_sched']").click(function(){
  dodeleterow($(this));
});

$("a[tag='add_sched']").click(function(){
    var nobj = $(this).parent().parent().parent().clone(true);
    
    $.when(
    $.ajax({url: "<?=site_url("process_/addnewrowrequest_dtr")?>",type: "POST",data: {field : "timein"},
        success: function(msg2){$(nobj).find(".timeindisplay").html(msg2);
        $(nobj).find("input[name='timein']").timepicker({
                minuteStep: 1,
                showSeconds: false,
                showMeridian: true,
                defaultTime: false
        });
        }
    })).then(
    $.ajax({url: "<?=site_url("process_/addnewrowrequest_dtr")?>",type: "POST",data: {field : "timeout"},
        success: function(msg2){$(nobj).find(".timeoutdisplay").html(msg2);
        $(nobj).find("input[name='timeout']").timepicker({
                minuteStep: 1,
                showSeconds: false,
                showMeridian: true,
                defaultTime: false
        }); 
        }
    }));
    
    $.ajax({url: "<?=site_url("process_/addnewrowrequest_dtr")?>",type: "POST",data: {field : "datelog",cutoffid : "<?=$cutoffid?>",employeeid : "<?=$employeeid?>"},
        success: function(msg2){$(nobj).find(".datelogdisplay").html(msg2);
        $("select[name='datelog']").val("");
        $("select[name='datelog']").attr("timeid","");
        $(nobj).find(".chosen").chosen();
        }
    });
    
    $(nobj).find("a[tag='delete_sched']").attr("timeid","");
    $(nobj).find("a[tag='delete_sched']").click(function(){
          dodeleterow($(this));
    });
    
    $(nobj).insertAfter($(this).parent().parent().parent());    
});
$("#saverequest").click(function(){
 $("tr[tag='rowrequest']").each(function(){
   var sobj = $(this);
   $.ajax({
      url: "<?=site_url("process_/saverequest_dtr")?>",      
      type: "POST",
      data: {cutoffid:"<?=$cutoffid?>",
             employeeid:"<?=$employeeid?>",
             timeid:$(sobj).find("select[name='datelog']").attr("timeid"),
             cdate:$(sobj).find("select[name='datelog']").val(),
             tfrom:$(sobj).find("input[name='timein']").val(),
             tto:$(sobj).find("input[name='timeout']").val()},
      success: function(msg){
        $(sobj).find("select[name='datelog']").attr("timeid",$(msg).find("timeid:eq(0)").text()); 
        $(sobj).find("a[tag='delete_sched']").attr("timeid",$(msg).find("timeid:eq(0)").text());
      }
   }); 
 });
 alert("Done saving.");
 return false;   
});
function dodeleterow(obj){
  if($("#idsum").find("tr[tag='rowrequest']").length>1){
      var sobj = $(obj);
      var timeid = $(sobj).attr("timeid");
      if(timeid){
        $.ajax({
            url: "<?=site_url("process_/deletetimeload_dtr")?>",
            type: "POST",
            data: {
                timeid:timeid,
                employeeid:"<?=$employeeid?>"
            },
            success: function(msg){
                $(sobj).parent().parent().parent().remove();
            }
        });
      }else{
        $(sobj).parent().parent().parent().remove();  
      }  
  }     
}
$("input[name='timein'],input[name='timeout']").timepicker({
        minuteStep: 1,
        showSeconds: false,
        showMeridian: true,
        defaultTime: false
}); 
$(".chosen").chosen();
</script>