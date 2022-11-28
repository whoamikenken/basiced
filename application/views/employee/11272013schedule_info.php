<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */

$employee_info = array();
# echo $employee_info[0]['employeeid'];
?>
<div style="background: #fff;position: relative;border-radius: 3px;max-height: 400px;overflow-x: hidden;overflow-y: scroll;">
 <table class="schedulelist">
    <tr>
      <th rowspan="2">Day of the Week</th>
      <th rowspan="2">&nbsp;</th>
      <th colspan="2">Schedule</th>
      <th rowspan="2">&nbsp;</th>
      <th rowspan="2">&nbsp;</th>
    </tr>
    <tr>
      <th>From</th>
      <th>To</th>
    </tr>
<?
$dow = array("M"=>"Monday","T"=>"Tuesday","W"=>"Wednesday","TH"=>"Thursday","F"=>"Friday","S"=>"Saturday","SUN"=>"Sunday");
foreach($dow as $dow_code => $dow_desc){
    $sql_schedperday = $this->db->query("SELECT DISTINCT starttime,endtime,dayofweek,type 
                                         FROM employee_schedule   
                                         WHERE dayofweek='{$dow_code}' AND employeeid='{$employee_info[0]['employeeid']}'");
   
    $sh = "";$sm = "";$ss = "";
    $eh = "";$em = "";$es = "";
    $type_l = "";
    if($sql_schedperday->num_rows()>0){
        $mrow_schedperday = $sql_schedperday->row(0);  
        $type_l = $mrow_schedperday->type;
        $sh = date("h",strtotime($mrow_schedperday->starttime));$sm = date("i",strtotime($mrow_schedperday->starttime));$ss = date("A",strtotime($mrow_schedperday->starttime));
        $eh = date("h",strtotime($mrow_schedperday->endtime));$em = date("i",strtotime($mrow_schedperday->endtime));$es = date("A",strtotime($mrow_schedperday->endtime));    
    }          
                     
?>    
    <tr tag='sched' code="<?=$dow_code?>">
      <td><?=$dow_desc?></td>
      <td><img tag='imgcopy' src='<?=base_url()?>images/copy.png' width="20px" title="copy"/></td>
      <td><select><?=$this->extras->showhours($sh)?></select>:<select><?=$this->extras->showminutes($sm)?></select> <select><?=$this->extras->showstat($ss)?></select></td>
      <td><select><?=$this->extras->showhours($eh)?></select>:<select><?=$this->extras->showminutes($em)?></select> <select><?=$this->extras->showstat($es)?></select></td>
      <td><?=form_dropdown("",$this->extras->showadjustment_code(true),$type_l,"")?></td>
      <td><img tag='addrow' src='<?=base_url()?>images/add.png' width="20px"/></td>
    </tr>
<?
    $type_l = "";
    for($c=1;$c<$sql_schedperday->num_rows();$c++){
        $mrow_schedperday = $sql_schedperday->row($c); 
        $type_l = $mrow_schedperday->type;
    # while($mrow_schedperday = mysql_fetch_array($sql_schedperday)){
        $sh = date("h",strtotime($mrow_schedperday->starttime));$sm = date("i",strtotime($mrow_schedperday->starttime));$ss = date("A",strtotime($mrow_schedperday->starttime));
        $eh = date("h",strtotime($mrow_schedperday->endtime));$em = date("i",strtotime($mrow_schedperday->endtime));$es = date("A",strtotime($mrow_schedperday->endtime));  
?>
    <tr tag='sched' code="<?=$dow_code?>">
      <td>&nbsp;</td>
      <td><img tag='imgcopy' src='<?=base_url()?>images/copy.png' width="20px" title="copy"/></td>
      <td><select><?=$this->extras->showhours($sh)?></select>:<select><?=$this->extras->showminutes($sm)?></select> <select><?=$this->extras->showstat($ss)?></select></td>
      <td><select><?=$this->extras->showhours($eh)?></select>:<select><?=$this->extras->showminutes($em)?></select> <select><?=$this->extras->showstat($es)?></select></td>
      <td><?=form_dropdown("",$this->extras->showadjustment_code(true),$type_l,"")?></td>
      <td><img tag='removerow' src='<?=base_url()?>images/delete.png' width="20px"/></td>
    </tr>
<?        
    }    
}
?>    
  </table>
</div>
<button id="saveschedulebutton" type="button" class='button_general'>Save Schedule</button>
<script>
function clone(obj1,obj2){
 //var clonewars = $(obj1).clone(true);
 //alert(clonewars);
 //$(obj2).html("");
 //$(obj2).append(clonewars);
 
 
 var con = 0;var vals = "";
 $(obj1).find("select").each(function(){
   vals = $(this).find("option:selected").val();
   $(obj2).find("select").eq(con).val(vals);
   con++;  
 });
 
 var con = 0;var vals = "";
 $(obj1).find("input").each(function(){
   vals = $(this).val();
   $(obj2).find("input").eq(con).val(vals); 
   
   if($(this).attr("type")=="checkbox"){
     var checkbox = $(this).attr("checked") ? true : false;
     $(obj2).find("input").eq(con).attr("checked",checkbox);
   } 
   con++;  
 });
 
 var con = 0;var vals = "";
 $(obj1).find("textarea").each(function(){
   vals = $(this).val(); 
   $(obj2).find("textarea").eq(con).val(vals);
   con++;  
 });
 
 var con = 0;var vals = "";
 $(obj1).find("button").each(function(){
   vals = $(this).val(); 
   $(obj2).find("button").eq(con).val(vals);
   con++;  
 });
 
}
var temp_obj;
function docopy(obj){
   var title_1 = ""; 
   var img_scr_1 = "";
   var title_2 = ""; 
   var img_scr_2 = ""; 
   var process = $(obj).attr("title"); 
   
   if(process=="paste"){
      title_1 = "copy"; 
      img_scr_1 = "<?=base_url()?>images/copy.png";
      title_2 = "copy"; 
      img_scr_2 = "<?=base_url()?>images/copy.png";
      
      /** paste details */
      clone($(temp_obj).parent().parent().find("td:eq(2)"),$(obj).parent().parent().find("td:eq(2)"));
      clone($(temp_obj).parent().parent().find("td:eq(3)"),$(obj).parent().parent().find("td:eq(3)"));
      clone($(temp_obj).parent().parent().find("td:eq(4)"),$(obj).parent().parent().find("td:eq(4)"));
      //clone($(temp_obj).parent().parent().find("td:eq(5)"),$(obj).parent().parent().find("td:eq(5)"));
      
      
      $(temp_obj).parent().parent().find("td").each(function(){
         $(this).css("background","#D0E2EE");
      });
   }else{
      temp_obj = $(obj);
      title_1 = "paste"; 
      img_scr_1 = "<?=base_url()?>images/paste.png";
      title_2 = "copy"; 
      img_scr_2 = "<?=base_url()?>images/copy.png";
      $(obj).parent().parent().find("td").each(function(){
         $(this).css("background","#5AB8E8");
      });
   }
    
    $("img[tag='imgcopy']").each(function(){ 
           $(this).attr("title",title_1); 
           $(this).attr("src",img_scr_1);
    });
    $(obj).attr("title",title_2);
    $(obj).attr("src",img_scr_2);
}
function doaddrow(obj){
    $(obj).parent()
          .parent()
          .clone()
          .insertAfter($(obj).parent().parent())
          .find("td:eq(0)").text("")
          .parent()
          .find("td:eq(1)")
          .find("img:eq(0)").click(function(){
            docopy($(this));
          })
          .parent().parent()
          .find("td:eq(5)")
          .html("<img tag='removerow' src='<?=base_url()?>images/delete.png' width='20px'>")
          .click(function(){
             $(this).parent().remove();
          });
        
    centerThis("shadeblock");        
}
function reform_string(str,len,c,lr){
   len = len ? len : 2;
   c = c ? c : "0";
   lr = lr ? lr : "left";   
   while(str.length<len){
     str = (lr=="left" ? c + "" + str : str + "" + c);
   } 
   return str;
}
function validate_time(val){
    var ret = true;
    if (!/^(1[012]|0[1-9]):[0-5][0-9]:[0-5][0-9](\\s)? (am|pm)+$/i.test(val)) {
        ret = false; 
    }
    return ret; 
}
$("img[tag='imgcopy']").click(function(){
   docopy($(this));
});
$("img[tag='addrow']").click(function(){
   doaddrow($(this));
});
$("img[tag='removerow']").click(function(){
   $(this).parent().parent().remove(); 
});

var allowcombinesection = false;
$("#saveschedulebutton").click(function(){
  /** checking values */
  updateinformation("employee/schedule_info",'');
});
</script>