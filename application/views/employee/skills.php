<?php

/**
 * @author Justin
 * @copyright 2015
 */
 
$aw_date = date("Y-m-d");
?>
<form id="form_skills">
<div class="form_row">
    <label class="field_name align_right">Skills</label>
    <div class="field">
        <input type="text" name="os_skills" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Years of Experience</label>
    <div class="field">
        <input type="text" name="os_years" class="col-md-8" value=""/>
    </div>
</div>
<div class="form_row">
    <label class="field_name align_right">Comments</label>
    <div class="field">
        <input type="text" name="os_comments" class="col-md-8" value=""/>
    </div>
</div>
</form>
<script>
$("#button_save_modal").unbind("click").click(function(){
    
      var cobj = "";
      $("#skillslist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='os_skills']").val());
         $(cobj).find("td:eq(1)").text($("input[name='os_years']").val());
         $(cobj).find("td:eq(2)").text($("input[name='os_comments']").val());         
      }else{       
         var mtable = $("#skillslist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='os_skills']").val()+"</td>"); 
         $(ntr).append("<td>"+$("input[name='os_years']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='os_comments']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addskills($(this));
         }).appendTo($(mtd));
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#skillslist").find("tbody"));      
      }  
      $("#modalclose").click();
       
       return false;
});
$('.chosen').chosen();
</script>