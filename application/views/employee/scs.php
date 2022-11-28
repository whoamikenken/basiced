<?php

/**
 * @author Kennedy
 * @copyright 2019
 */
$sm_date = date("Y-m-d");
 
?>
<form id="form_education" class="form-horizontal">
<div class="form-group">
  <div class="col-sm-4 align_center">
    <input type="radio" name="sccType" value="Seminar" checked="" /> Seminar
  </div>
    <div class="col-sm-4 align_center">
      <input type="radio" name="sccType" value="Conventions"/> Conventions
  </div>
    <div class="col-sm-4 align_center">
      <input type="radio" name="sccType" value="Conference"/> Conference 
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
  <label class="col-sm-4 control-label">Educational Level</label>
  <div class="col-sm-7">
    <select class="form-control" name="pi_educlevel"  >
    <?
        $educlevel = $this->extras->showreportseduclevel('','PI');
        foreach($educlevel as $c=>$val){

        ?><option value="<?=$c?>" ><?=$val?></option><?    
        }
    ?>
    </select>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Seminar Title</label>
  <div class="col-sm-7">
    <input type="text" name="eb_honor" class="form-control" value=""/>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Speaker</label>
  <div class="col-sm-7">
    <input type="text" name="eb_speaker" class="form-control" value=""/>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Address</label>
  <div class="col-sm-7">
    <input type="text" name="eb_address" class="form-control" value=""/>
  </div>
</div>

<div class="form-group">
  <label class="col-sm-4 control-label">Date Attended</label>
  <div class="col-sm-7">
    <div class='input-group date date_issued' data-date="<?=date("Y-m-d",strtotime($sm_date))?>" data-date-format="yyyy-mm-dd">
        <input type='text' class="form-control" size="16" name="sm_date" value="<?=date("Y-m-d",strtotime($sm_date))?>"/>
        <span class="input-group-addon">
            <span class="glyphicon glyphicon-calendar"></span>
        </span>
    </div>
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
              required: true
              // minlength: 2
            }
        }
    });
    
   if($("#form_education").valid()){
      var cobj = "";
      $("#scsinfolist").find("tbody tr").each(function(){
        if($(this).attr("iscurrent")==1) cobj = $(this);
      });            
      console.log(cobj);  
      if(cobj){
         $(cobj).find("td:eq(0)").text($("select[name='pi_educlevel']").val());
         $(cobj).find("td:eq(1)").text($("input[name='sm_date']").val());
         $(cobj).find("td:eq(2)").text($("input[name='eb_honor']").val());  
         $(cobj).find("td:eq(3)").text($("input[name='sccLocation']").val());
         $(cobj).find("td:eq(4)").text($("input[name='eb_address']").val());
         $(cobj).find("td:eq(5)").text($("input[name='eb_speaker']").val());
         // $(cobj).find("td:eq(6)").text($("input[name='sccType']:checked").val());  
      }else{       
         var mtable = $("#scsinfolist").find("tbody");
         if($(mtable).find("tr:first").find("td").length==1) $(mtable).html("");
         var ntr = $("<tr></tr>");
         $(ntr).append("<td>"+$("select[name='pi_educlevel']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='sm_date']").val()+"</td>");
        /* $(ntr).append("<td>"+$("select[name='eb_level']").val()+"</td>");*/
         $(ntr).append("<td>"+$("input[name='eb_honor']").val()+"</td>");
         $(ntr).append("<td>"+$("select[name='sccLocation']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_address']").val()+"</td>");
         $(ntr).append("<td>"+$("input[name='eb_speaker']").val()+"</td>");
         // $(ntr).append("<td>"+$("input[name='sccType']").val()+"</td>");
         var mtd = $("<td class='align_center'></td>");
         
         $("<a class='btn btn-danger' href='#modal-view' data-toggle='modal'><i class='glyphicon glyphicon-edit'></i></a>").click(function(){
            addscs($(this));
         }).appendTo($(mtd));
         
         $("<a class='btn btn-danger'><i class='glyphicon glyphicon-trash'></i></a>").click(function(){
            $(this).parent().parent().remove();
            if($(mtable).find("tr:first").find("td").length==0) $(mtable).append("<tr><td colspan='4'>No existing data</td></tr>");
         }).appendTo($(mtd));
         
         $(ntr).append($(mtd));
         $(ntr).appendTo($("#scsinfolist").find("tbody"));      
      }  
      $("#modalclose").click();
       
       return false;
   }else {
       $validator.focusInvalid();
       return false;
   }
   
});
$(".date_issued").datetimepicker({
    format: "YYYY-MM-DD"
});
$('.chosen').chosen();
</script>