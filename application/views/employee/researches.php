<?php

/**
 * @author Justin
 * @copyright 2016
 */
 
?>
<form id="form_education" class="form-horizontal">

<div class="form-group">
  <label class="col-sm-4 control-label">Date Published</label>
  <div class="col-sm-7">
    <div class='input-group date eb_date' data-date="<?=date("Y-m-d")?>"  data-date-format="yyyy-mm-dd">
        <input type='text' class="form-control" size="16" name="eb_date"/>
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
    </div>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Type of Research</label>
  <div class="col-sm-7">
    <select class="form-control" name="r_educlevel"  >
      <?
        $educlevel = $this->extras->showreportseduclevel('','R');
        foreach($educlevel as $c=>$val){

        ?><option value="<?=$c?>" ><?=$val?></option><?    
        }
      ?>
      </select>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Research Title</label>
  <div class="col-sm-7">
    <input type="text" name="eb_yeargrad" class="form-control" value=""/>
  </div>
</div>

<div class="form_row" id="dm">
    <!-- <label class="field_name align_right">Honors</label>
    <div class="field">
        <input type="text" name="eb_honor" class="col-md-10" value=""/>
    </div> -->
</div>
</form>
<script>
$(".eb_date").datetimepicker({
    format: "YYYY-MM-DD"
});
$("#button_save_modal").unbind("click").click(function(){
 var $validator = $("#form_education").validate({
        rules: {
            r_educlevel: {
              // required: true
            },
            eb_date: {
              required: true,
              minlength: 2
            },
            eb_yeargrad: {
              required: true,
              minlength: 2
            },
            eb_honor: {
              required: true,
              minlength: 2
            }
        }
    });
    
   if($("#form_education").valid()){
      var cobj = "";
      $("#researchesinfolist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });              
      if(cobj){
         $(cobj).find("td:eq(0)").text($("input[name='eb_date']").val());
         $(cobj).find("td:eq(1)").text($("select[name='r_educlevel']").val());
         $(cobj).find("td:eq(2)").text($("input[name='eb_yeargrad']").val());  
         // $(cobj).find("td:eq(3)").text($("input[name='eb_honor']").val());                                  
      }else{       
         var mtable = $("#researchesinfolist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("input[name='eb_date']").val()+"</td>");
       /*  $(ntr).append("<td>"+$("select[name='eb_level']").val()+"</td>");*/
          $(ntr).append("<td educr='"+$("select[name='r_educlevel'] :selected").val()+"'>"+$("select[name='r_educlevel'] :selected").text()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_yeargrad']").val()+"</td>");
         // $(ntr).append("<td>"+$("input[name='eb_honor']").val()+"</td>");
         
         var mtd = $("<td class='align_center'></td>");
         /*
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addresearches($(this));
         }).appendTo($(mtd));
         */
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#researchesinfolist").find("tbody"));      
      }  
      $("#modalclose").click();
       
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
   
});
$(".birthdate_lr").datepicker({
    autoclose: true,
    todayBtn: true
});
$('.chosen').chosen();
</script>