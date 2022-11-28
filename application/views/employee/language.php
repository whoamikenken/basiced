<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
$aw_date = date("Y-m-d");
?>
<form id="form_language">
<div class="form_row">
    <label class="field_name align_right">Language</label>
    <div class="field">
        <input type="text" name="lang_language" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Skill</label>
    <div class="field">
        <input type="text" name="lang_skill" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Comments</label>
    <div class="field">
        <input type="text" name="lang_comment" class="col-md-8" value=""/>
    </div>
</div>
</form>
<script>
$("#button_save_modal").unbind("click").click(function(){
    
      var cobj = "";
      $("#languagelist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='lang_language']").val());
         $(cobj).find("td:eq(1)").text($("input[name='lang_skill']").val());
         $(cobj).find("td:eq(2)").text($("input[name='lang_comment']").val());         
      }else{       
         var mtable = $("#languagelist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='lang_language']").val()+"</td>"); 
         $(ntr).append("<td>"+$("input[name='lang_skill']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='lang_comment']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addskills($(this));
         }).appendTo($(mtd));
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#languagelist").find("tbody"));      
      }  
      $("#modalclose").click();
       
       return false;
});
$('.chosen').chosen();
</script>