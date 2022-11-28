<form id="form_emergencyContact">
	<div class="form_row">
		<label class="field_name align_right">Skills Name</label>
		<div class="field">
			<input type="text" name="eb_name" class="col-md-10" value=""/>
		</div>
	</div>
	<div class="form_row">
		<label class="field_name align_right">Year of Use</label>
		<div class="field">
			<input type="number" name="eb_yearOfUse" class="col-md-10" value=""/>
		</div>
	</div>
	<div class="form_row">
		<label class="field_name align_right">Level</label>
		<div class="field">
			<input type="text" name="eb_level" class="col-md-10" value=""/>
		</div>
	</div>
</form>
<script>
$("#button_save_modal").unbind("click").click(function(){
 var $validator = $("#form_emergencyContact").validate({
        rules: {
            eb_name: {
              required: true,
              minlength: 2
            },
			eb_yearOfUse: {
              required: true
            },
			eb_level: {
              required: true,
              minlength: 2
            }
        }
    });
    
   if($("#form_emergencyContact").valid()){
      var cobj = "";
      $("#skilllist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='eb_name']").val());
         $(cobj).find("td:eq(1)").text($("input[name='eb_yearOfUse']").val());
         $(cobj).find("td:eq(2)").text($("input[name='eb_level']").val());                                                             
      }else{       
         var mtable = $("#skilllist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='eb_name']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_yearOfUse']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_level']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addskill($(this));
         }).appendTo($(mtd));
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#skilllist").find("tbody"));      
      }
      $("#modalclose").click();
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
   
});
</script>