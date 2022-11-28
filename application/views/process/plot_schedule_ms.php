<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
 
$q = $this->db->query("SELECT a.cdate,a.starttime,a.endtime,a.`dayofweek`,a.`idx`,a.`type` 
                       FROM employee_schedule_percutoff a
                       INNER JOIN cutoff_details b ON b.days_=a.`idx` AND b.id=a.cutoffid AND b.cdate=a.cdate 
                       WHERE a.employeeid='$employeeid' AND b.id='{$cutoffid}'
                       UNION
                       select DISTINCT a.cdate,a.starttime,a.endtime,a.`dayofweek`,a.`idx`,a.`type`
                       from employee_schedule_percutoff a
                       inner join employee_schedule_adjustment b ON b.`idx`=a.`idx` AND b.cutoffid=a.cutoffid AND b.cdate=a.cdate 
                       where b.cutoffid='{$cutoffid}' and a.employeeid='{$employeeid}' ORDER BY cdate,starttime");
                       
if($q->num_rows()==0){
 $q = $this->db->query("SELECT b.cdate,a.starttime,a.endtime,a.`dayofweek`,a.`idx`,a.`type`,'' as `remarks` 
                        FROM employee_schedule a 
                        INNER JOIN cutoff_details b ON b.days_=a.`idx`
                        WHERE a.employeeid='{$employeeid}' AND b.id='{$cutoffid}'
                        UNION
                        select DISTINCT b.cdate,b.starttime,b.endtime,b.`dayofweek`,b.`idx`,b.`type`,'' as `remarks` 
                        from employee_schedule_adjustment b 
                        where b.cutoffid='{$cutoffid}' and b.employeeid='{$employeeid}' ORDER BY cdate,starttime");    
}                       
?>
<div class="well-content" style='border: transparent !important;'>
<table class="table table-striped table-bordered table-hover datatable">
    <thead>
  <tr>
    <th>Date</th>
    <th>From</th>
    <th>To</th>
    <th>Type</th>
    <th class="col-md-1"></th>
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
            <select class='chosen' name='datelog'>";
            <?
              $opt_type = $this->extras->showcutofdatebyid($cutoffid,$employeeid);
              foreach($opt_type as $t=>$val){
            ?><option<?=($t==$mrow->cdate ? " selected='true'" : "")?> value='<?=$t?>'><?=$val?></option><?    
              }   
            ?>  
            </select>
        </div>
    </td>
    <td class="timeindisplay">
      <div class='input-group bootstrap-timepicker'>
            <input name='timein' class='col-md-8 input-small align_center' type='text' value='<?=date("h:i A",strtotime($mrow->starttime))?>'/>
            <span class='add-on'><i class='glyphicon glyphicon-time'></i></span>
      </div>
    </td>
    <td class="timeoutdisplay">
      <div class='input-group bootstrap-timepicker'>
            <input name='timeout' class='col-md-8 input-small align_center' type='text' value='<?=date("h:i A",strtotime($mrow->endtime))?>'/>
            <span class='add-on'><i class='glyphicon glyphicon-time'></i></span>
      </div> 
    </td>
    <td class='typedisplay'>
        <div class='col-md-4 no-search'>
            <select class='chosen' name='schedtype'>";
            <?
              $opt_type = $this->extras->showadjustment_code(false);
              foreach($opt_type as $t=>$val){
            ?><option<?=($t==$mrow->type ? " selected" : "")?> value='<?=$t?>'><?=$val?></option><?    
              }   
            ?>  
            </select>
        </div>
    </td>
    <td class="col-md-1 buttondisplay">
        <div class='btn-group'>
            <a class='btn' href='#' tag='add_sched'><i class='glyphicon glyphicon-plus'></i></a>
            <a class='btn' href='#' tag='delete_sched'><i class='glyphicon glyphicon-trash'></i></a>
        </div>
    </td>
  </tr>
<?        
    
}
}else{
?>
  <tr>
    <td colspan="5" align='center'><i>No existing data</i></td>
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
function dodeleterow(obj){
  if($("#idsum").find("tr[tag='rowrequest']").length>1){
      var sobj = $(obj);
        $(sobj).parent().parent().parent().remove();    
  }     
}
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
        $(nobj).find("select[name='datelog']").val("");
        $(nobj).find(".chosen").chosen();
        }
    });
    
    $.ajax({url: "<?=site_url("process_/addnewrowrequest")?>",type: "POST",data: {field : "type"},
        success: function(msg2){$(nobj).find(".typedisplay").html(msg2);
        $(nobj).find(".chosen").chosen();
        }
    });
    
    $(nobj).find("a[tag='delete_sched']").click(function(){
          dodeleterow($(this));
    });
    
    $(nobj).insertAfter($(this).parent().parent().parent());    
});
$("#saverequest").click(function(){
 $.when($.ajax({
    url: "<?=site_url("process_/clearschedfirst")?>",
    type: "POST",
    data: {
        cutoffid:"<?=$cutoffid?>",
        employeeid:"<?=$employeeid?>"
    },
    success: function(msg){
        // just do the deleting
    }  
 })).done(function(){
    $("tr[tag='rowrequest']").each(function(){
       var sobj = $(this); 
    
       $.ajax({
          url: "<?=site_url("process_/saverequest_ms")?>",      
          type: "POST",
          data: {cutoffid:"<?=$cutoffid?>",
                 employeeid:"<?=$employeeid?>",
                 cdate:$(sobj).find("select[name='datelog']").val(),
                 tfrom:$(sobj).find("input[name='timein']").val(),
                 tto:$(sobj).find("input[name='timeout']").val(),
                 ctype:$(sobj).find("select[name='schedtype']").val()},
          success: function(msg){
            //alert(msg);
          }
       }); 
     });
     alert("Done saving.");
 });   
 return false;   
});
$("input[name='timein'],input[name='timeout']").timepicker({
        minuteStep: 1,
        showSeconds: false,
        showMeridian: true,
        defaultTime: false
}); 
$(".chosen").chosen();
</script>