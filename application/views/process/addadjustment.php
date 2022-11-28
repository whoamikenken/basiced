<?php

 $cdate = "";
 $starttime = "";
 $endtime = "";
 $dayofweek = "";
 $remarks = "";
 #echo $chkbox." / ".$adateid." / ".$uid." / ";
 if($chkbox == "chkedit"){
         #$sql = $this->db->query("SELECT cdate,starttime,endtime,`dayofweek`,idx,remarks from employee_schedule_adjustment WHERE id='{$adateid}'")->result();
         $sql = $this->db->query("SELECT a.cdate,SUBSTR(b.timein,1,10) as tdate, b.timein, b.timeout,`dayofweek`,idx,remarks from 
                                    employee_schedule_adjustment a
                                    RIGHT JOIN timesheet b ON a.employeeid = b.userid 
                                    WHERE b.timeid='{$adateid}'")->result();
         if(count($sql)>0){
          foreach($sql as $mrow){
            #$cdate = ($mrow->cdate != "") ? $mrow->cdate : $mrow->tdate;
            $cdate = $mrow->tdate;                        
            #$starttime = ($mrow->starttime != "") ? date("h:i A",strtotime($mrow->starttime)) : "";
            #$endtime = ($mrow->endtime != "") ? date("h:i A",strtotime($mrow->endtime)) : "";
            $starttime = ($mrow->timein != "" && $mrow->timein != "0000-00-00 00:00:00") ? date("h:i A",strtotime($mrow->timein)) : "";
            $endtime = ($mrow->timeout != "" && $mrow->timeout != "0000-00-00 00:00:00") ? date("h:i A",strtotime($mrow->timeout)) : "";
            $dayofweek = $mrow->dayofweek;
            $remarks = $mrow->remarks;
          }
         }
 }else if($chkbox == "chkdate"){
    $cdate = $dto;
 }else{
     if($adateid){
         $sql = $this->db->query("SELECT cdate,starttime,endtime,`dayofweek`,idx,remarks from employee_schedule_adjustment WHERE id='{$adateid}' and employeeid='{$uid}'")->result();
         #$sql = $this->db->query("SELECT a.cdate, b.timein, b.timeout,`dayofweek`,idx,remarks from employee_schedule_adjustment aRIGHT JOIN timesheet b ON a.employeeid = b.userid WHERE b.timeid='{$adateid}' and employeeid='{$uid}'")->result();
         if(count($sql)>0){
          foreach($sql as $mrow){  
            $cdate = $mrow->cdate;
            $starttime = ($mrow->starttime != "") ? date("h:i A",strtotime($mrow->starttime)) : "";
            $endtime = ($mrow->endtime != "") ? date("h:i A",strtotime($mrow->endtime)) : "";
            $dayofweek = $mrow->dayofweek;
            $remarks = $mrow->remarks;
          }
         }
     }
 }
?>
<form id="form_adjustment" method="POST" action="#">
<div class="form_row">
    <label class="field_name align_right">Date</label>
    <div class="field">
        <div class="input-group date" id="dp2" data-date="<?=$cdate?>" data-date-format="yyyy-mm-dd">
            <input class="align_center" id="u_date" name="u_date" size="16" type="text" value="<?=$cdate?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Time In</label>
    <div class="field">
        <div class="input-group bootstrap-timepicker">
            <input id="u_timein" name="u_timein" class="col-md-8 input-small align_center" type="text" value="<?=$starttime?>"/>
            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
        </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Time Out</label>
    <div class="field">
        <div class="input-group bootstrap-timepicker">
            <input id="u_timeout" name="u_timeout" class="col-md-8 input-small align_center" type="text" value="<?=$endtime?>"/>
            <span class="add-on"><i class="glyphicon glyphicon-time"></i></span>
        </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Remarks</label>
    <div class="field no-search">
        <select id="u_remarks" name="u_remarks" class="form-control"><?=$this->extras->showrequesttype($remarks)?></select>
    </div>
</div>
<script>
/*
$("#u_date").change(function () {
  var date = $("#u_date").val();
  var uid = <?php print("\"" . $uid . "\";"); ?>

  $.ajax({
    url: "<?=site_url("process_/loadTimesForAdjustment")?>",
    data : {'logdate':date,'userid':uid},
    type : "POST",
    success:function(data){
      // console.log(data);
      var datum = JSON.parse(data);
      $("#u_timein").val(datum.timein);
      $("#u_timeout").val(datum.timeout);
    }
  }); 


  // alert(date + " -> " + uid);
});
*/

$(function(){
   $("#button_save_modal").unbind("click").click(function(){
       $("#form_adjustment").submit();
    }); 
    $("#form_adjustment").submit(function(){
       var cancontinue = true;
       if($("#u_date").val()==""){
          $("#u_date").parent().parent().parent().removeClass("control-group").removeClass("error");
          $("#u_date").parent().parent().find(".help-inline").remove();
          
          $("#u_date").parent().parent().parent().addClass("control-group").addClass("error");
          $("#u_date").parent().parent().append("<span class='help-inline'>Date is required.</span>");
          cancontinue = false;
       }
       
        if($("#u_timein").val()=="" && $("#u_timeout").val()==""){
           $("#u_timein,#u_timeout").parent().parent().parent().removeClass("control-group").removeClass("error");
           $("#u_timein").parent().parent().find(".help-inline").remove();
          
           $("#u_timein,#u_timeout").parent().parent().parent().addClass("control-group").addClass("error");
           $("#u_timein").parent().parent().append("<span class='help-inline'>Either one of the time is required.</span>");
           cancontinue = false;
        }
       
       if($("#u_remarks option:selected").val()==""){
          $("#u_remarks").parent().parent().parent().removeClass("control-group").removeClass("error");
          $("#u_remarks").parent().parent().find(".help-inline").remove();
        
          $("#u_remarks").parent().parent().addClass("control-group").addClass("error");
          $("#u_remarks").parent().append("<span class='help-inline'>Remarks is required.</span>");
          cancontinue = false;
       }
       
       if(cancontinue){
        //alert("<?=$adateid?>");
        
           $.ajax({
              url: "<?=site_url("process_/saverequest")?>",
              data : $("#form_adjustment").serialize() + "&adateid=<?=$adateid?>&uid=<?=$uid?>&chkbox=<?=$chkbox?>",
              type : "POST",
              success:function(msg){
                ulist.fnDraw();
                $("#modalclose").click();
              }
           });
        
       }
       return false;
    });
    
    $("#u_date").change(function(){
        /*
       if($(this).val()!=""){
          $(this).parent().parent().parent().removeClass("control-group").removeClass("error");
          $(this).parent().parent().find(".help-inline").remove();
       }  
       */
          $.ajax({
            url     :   "<?=site_url("process_/showtimedtr")?>",
            type    :   "POST",
            data    :   {
                            uid     : "<?=$uid?>",
                            tdate   : $(this).val()
                        },
            success : function(msg){
                $("#u_timein").val($(msg).find("timein").text());
                $("#u_timeout").val($(msg).find("timeout").text());
                console.log(msg);
            }
          });
    });
    
    $("#u_timein,#u_timeout").change(function(){
       if($(this).val()!=""){
          $("#u_timein").parent().parent().parent().removeClass("control-group").removeClass("error");
          $("#u_timein").parent().parent().find(".help-inline").remove();
          
          $("#u_timeout").parent().parent().parent().removeClass("control-group").removeClass("error");
          $("#u_timeout").parent().parent().find(".help-inline").remove();
       }  
    });
    
    $("#u_remarks").change(function(){
       if($(this).find("option:seleceted").val()!=""){
          $(this).parent().parent().removeClass("control-group").removeClass("error");
          $(this).parent().find(".help-inline").remove();
       }  
    });
});
$('#dp2').datepicker({
    autoclose: true
}); 
$('#u_timeout,#u_timein').timepicker({
    minuteStep: 1,
    showSeconds: false,
    showMeridian: true,
    defaultTime: false
}); 
$(".chosen").chosen();
</script>