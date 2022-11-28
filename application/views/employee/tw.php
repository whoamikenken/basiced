<?php

/**
 * @author Kennedy
 * @copyright 2019
 */
 
?>
<form id="form_education" class="form-horizontal">

<div class="form-group">
  <label class="col-sm-4 control-label">Type</label>
  <div class="col-sm-7">
    <div class="col-md-6">
      <input type="radio" name="sccType" value="Training" checked="" /> Training
    </div>
    <div class="col-md-6">
      <input type="radio" name="sccType" value="Workshop"/> Workshop
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Location</label>
  <div class="col-sm-7">
    <select name="sccLocation" class="form-control">
      <option value="Local">Local</option>
      <option value="Regional">Regional</option>
      <option value="National">National</option>
      <option value="International">International</option>
    </select>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Name of School</label>
  <div class="col-sm-7">
    <input type="text" name="eb_school" class="form-control" value=""/>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Educational Level</label>
  <div class="col-sm-7">
    <select class="form-control" name="tw_educlevel"  >
    <?
      $educlevel = $this->extras->showreportseduclevel('','TW');
      foreach($educlevel as $c=>$val){

      ?><option value="<?=$c?>" ><?=$val?></option><?    
      }
    ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Year Graduated</label>
  <div class="col-sm-7">
    <input type="text" name="eb_yeargrad" class="form-control" value=""/>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Honors</label>
  <div class="col-sm-7">
    <input type="text" name="eb_honor" class="form-control" value=""/>
  </div>
</div>
</form>
<script>
$("#button_save_modal").unbind("click").click(function(){
 var $validator = $("#form_education").validate({
        rules: {
            eb_level: {
              required: true
            },
            eb_school: {
              required: true,
              minlength: 2
            }
        }
    });
    
   if($("#form_education").valid()){
      var cobj = "";
      $("#twinfolist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='eb_school']").val());
         $(cobj).find("td:eq(1)").text($("select[name='eb_level']").val());
         $(cobj).find("td:eq(2)").text($("input[name='eb_yeargrad']").val());  
         $(cobj).find("td:eq(3)").text($("input[name='eb_honor']").val());
         $(cobj).find("td:eq(4)").text($("input[name='sccType']:checked").val());  
         $(cobj).find("td:eq(5)").text($("select[name='sccLocation']").val());                                      
      }else{       
         var mtable = $("#twinfolist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='eb_school']").val()+"</td>");
         /*$(ntr).append("<td>"+$("select[name='eb_level']").val()+"</td>");*/
         $(ntr).append("<td eductw='"+$("select[name='tw_educlevel'] :selected").val()+"'>"+$("select[name='tw_educlevel'] :selected").text()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_yeargrad']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_honor']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sccType']:checked").val()+"</td>");
         $(ntr).append("<td>"+$("select[name='sccLocation']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addtw($(this));
         }).appendTo($(mtd));
         
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#twinfolist").find("tbody"));      
      }  
      $("#modalclose").click();
       
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
   
});
$(".birthdate_lr").datetimepicker({
    format: "YYYY-MM-DD"
});
$('.chosen').chosen();
</script>