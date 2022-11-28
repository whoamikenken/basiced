<?php

/**
 * @author Aaron P. Ruanto
 * @copyright 2013
 */
$af_registration = date("Y-m-d");
?>
<form id="form_affiliate">
<div class="form_row">
    <label class="field_name align_right">Position</label>
    <div class="field">
        <input type="text" name="af_position" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Name of Organization</label>
    <div class="field">
        <input type="text" name="af_organization" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Date of Registration</label>
    <div class="field">
        <div class="input-group date date_issued" data-date="<?=date("Y-m-d",strtotime($af_registration))?>" data-date-format="yyyy-mm-dd">
            <input class="align_center" size="16" name="af_registration" type="text" value="<?=date("Y-m-d",strtotime($af_registration))?>" readonly>
            <span class="add-on"><i class="glyphicon glyphicon-calendar"></i></span>
        </div>
    </div>
</div>
<!--
<div class="form_row">
    <label class="field_name align_right"></label>
    <div class="field">
        <a href="#" class="btn btn-primary" id="saveaffiliation">Save</a>
    </div>
</div>
--!>
</form>
<script>
$("#button_save_modal").unbind("click").click(function(){
//$("#saveaffiliation").click(function(){
 var $validator = $("#form_affiliate").validate({
        rules: {
            af_position: {
              required: true
            },
            af_organization: {
              required: true,
              minlength: 2
            }
        }
    });
    
   if($("#form_affiliate").valid()){
      var cobj = "";
      $("#affiliationlist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
        
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='af_position']").val());
         $(cobj).find("td:eq(1)").text($("input[name='af_organization']").val());
         $(cobj).find("td:eq(2)").text($("input[name='af_registration']").val());         
      }else{       
         var mtable = $("#affiliationlist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='af_position']").val()+"</td>"); 
         $(ntr).append("<td>"+$("input[name='af_organization']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='af_registration']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addaffiliation($(this));
         }).appendTo($(mtd));
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#affiliationlist").find("tbody"));      
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