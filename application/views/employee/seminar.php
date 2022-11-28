<?php

/**
 * @author Justin
 * @copyright 2016
 */
$sm_date = date("Y-m-d");
?>
<form id="form_seminar">
<div class="form_row">
    <label class="field_name align_right">Title</label>
    <div class="field">
        <input type="text" name="sm_title" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Place</label>
    <div class="field">
        <input type="text" name="sm_place" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Date</label>
    <div class="field">
        <div class="input-group date date_issued" data-date="<?=date("Y-m-d",strtotime($sm_date))?>" data-date-format="yyyy-mm-dd">
            <input class="align_center" size="16" name="sm_date" type="text" value="<?=date("Y-m-d",strtotime($sm_date))?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Resource Speaker</label>
    <div class="field">
        <input type="text" name="sm_speaker" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Credit Earned</label>
    <div class="field">
        <input type="text" name="sm_credit" class="col-md-8" value=""/>
    </div>
</div>
<!--
<div class="form_row">
    <label class="field_name align_right"></label>
    <div class="field">
        <a href="#" class="btn btn-primary" id="saveseminar">Save</a>
    </div>
</div>
--!>
</form>
<script>
$("#button_save_modal").unbind("click").click(function(){
//$("#saveseminar").click(function(){
 var $validator = $("#form_seminar").validate({
        rules: {
            sm_title: {
              required: true
            },
            sm_place: {
              required: true
            }
        }
    });
    
   if($("#form_seminar").valid()){
      var cobj = "";
      $("#seminarinfolist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='sm_title']").val());
         $(cobj).find("td:eq(1)").text($("input[name='sm_place']").val());
         $(cobj).find("td:eq(2)").text($("input[name='sm_date']").val());         
         $(cobj).find("td:eq(3)").text($("input[name='sm_speaker']").val());
         $(cobj).find("td:eq(4)").text($("input[name='sm_credit']").val());
      }else{       
        
         var mtable = $("#seminarinfolist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='sm_title']").val()+"</td>"); 
         $(ntr).append("<td>"+$("input[name='sm_place']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_date']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_speaker']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_credit']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addseminar($(this));
         }).appendTo($(mtd));
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='6'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         
         $(ntr).appendTo($(mtable));      
      }  
      $("#modalclose").click();
       
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
});
$(".date_issued").datepicker({
    autoclose: true,
    todayBtn: true
});
$('.chosen').chosen();
</script>